// WOFF 1.0 (https://www.w3.org/TR/WOFF/): the font's tables, each compressed with zlib on its own.
// The browser's CompressionStream does the zlib part, so this needs no library.

import { tables } from './sfnt.js';

const pad4 = (n) => (n + 3) & ~3;

async function pipe(bytes, transform) {
    const stream = new Blob([bytes]).stream().pipeThrough(transform);

    return new Uint8Array(await new Response(stream).arrayBuffer());
}

/** Turns a TrueType or OpenType file into a WOFF file. */
export async function encode(sfnt) {
    const view = new DataView(sfnt.buffer, sfnt.byteOffset, sfnt.byteLength);
    const entries = tables(sfnt).sort((a, b) => (a.tag < b.tag ? -1 : 1));

    // A table is stored as is when compressing doesn't make it smaller, as the spec asks.
    const blocks = await Promise.all(
        entries.map(async (entry) => {
            const data = sfnt.subarray(entry.offset, entry.offset + entry.length);
            const packed = await pipe(data, new CompressionStream('deflate'));

            return packed.length < data.length ? packed : data;
        }),
    );

    const directoryEnd = 44 + entries.length * 20;
    const length = blocks.reduce((end, block) => end + pad4(block.length), directoryEnd);
    const out = new Uint8Array(length);
    const header = new DataView(out.buffer);

    // The version of the WOFF file follows the font's own revision, like sfnt2woff and fontTools do.
    const head = entries.find((entry) => entry.tag === 'head');
    const revision = head ? view.getInt32(head.offset + 4) / 65536 : 1;

    header.setUint32(0, 0x774f4646); // 'wOFF'
    header.setUint32(4, view.getUint32(0)); // the original flavour: TrueType or CFF
    header.setUint32(8, length);
    header.setUint16(12, entries.length);
    header.setUint32(16, entries.reduce((size, entry) => size + pad4(entry.length), 12 + entries.length * 16));
    header.setUint16(20, Math.trunc(revision));
    header.setUint16(22, Math.round((revision % 1) * 1000));

    let offset = directoryEnd;
    entries.forEach((entry, i) => {
        const record = 44 + i * 20;

        for (let c = 0; c < 4; c++) {
            out[record + c] = entry.tag.charCodeAt(c);
        }
        header.setUint32(record + 4, offset);
        header.setUint32(record + 8, blocks[i].length);
        header.setUint32(record + 12, entry.length);
        header.setUint32(record + 16, entry.checksum);

        out.set(blocks[i], offset);
        offset += pad4(blocks[i].length);
    });

    return out;
}

/** Unpacks a WOFF file back into the TrueType or OpenType file inside it. */
export async function decode(woff) {
    const view = new DataView(woff.buffer, woff.byteOffset, woff.byteLength);
    const count = view.getUint16(12);

    const entries = Array.from({ length: count }, (_, i) => {
        const record = 44 + i * 20;

        return {
            tag: view.getUint32(record),
            offset: view.getUint32(record + 4),
            packed: view.getUint32(record + 8),
            length: view.getUint32(record + 12),
            checksum: view.getUint32(record + 16),
        };
    });

    const blocks = await Promise.all(
        entries.map((entry) => {
            const data = woff.subarray(entry.offset, entry.offset + entry.packed);

            return entry.packed < entry.length ? pipe(data, new DecompressionStream('deflate')) : data;
        }),
    );

    const out = new Uint8Array(view.getUint32(16));
    const sfnt = new DataView(out.buffer);
    const searchRange = 2 ** Math.floor(Math.log2(count)) * 16;

    sfnt.setUint32(0, view.getUint32(4));
    sfnt.setUint16(4, count);
    sfnt.setUint16(6, searchRange);
    sfnt.setUint16(8, Math.floor(Math.log2(count)));
    sfnt.setUint16(10, count * 16 - searchRange);

    let offset = 12 + count * 16;
    entries.forEach((entry, i) => {
        const record = 12 + i * 16;

        sfnt.setUint32(record, entry.tag);
        sfnt.setUint32(record + 4, entry.checksum);
        sfnt.setUint32(record + 8, offset);
        sfnt.setUint32(record + 12, entry.length);

        out.set(blocks[i], offset);
        offset += pad4(entry.length);
    });

    return out.subarray(0, offset);
}
