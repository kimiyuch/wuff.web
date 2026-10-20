// Reading TrueType and OpenType files (the "sfnt" format): which kind of file it is, its tables,
// and the names and numbers the @font-face rule needs.

const tag = (view, offset) => String.fromCharCode(...[0, 1, 2, 3].map((i) => view.getUint8(offset + i)));

const dataView = (bytes) => new DataView(bytes.buffer, bytes.byteOffset, bytes.byteLength);

/** Tells TrueType, OpenType (CFF), WOFF, WOFF2 and font collections apart by their first four bytes. */
export function detect(bytes) {
    if (bytes.byteLength < 12) {
        return null;
    }

    const view = dataView(bytes);
    const signature = view.getUint32(0);

    if (signature === 0x00010000 || tag(view, 0) === 'true') {
        return 'ttf';
    }

    return { OTTO: 'otf', wOFF: 'woff', wOF2: 'woff2', ttcf: 'ttc' }[tag(view, 0)] ?? null;
}

/** The table directory of an sfnt: each table's tag, checksum, offset and length. */
export function tables(sfnt) {
    const view = dataView(sfnt);
    const count = view.getUint16(4);

    return Array.from({ length: count }, (_, i) => {
        const record = 12 + i * 16;

        return {
            tag: tag(view, record),
            checksum: view.getUint32(record + 4),
            offset: view.getUint32(record + 8),
            length: view.getUint32(record + 12),
        };
    });
}

function table(sfnt, name) {
    const entry = tables(sfnt).find((t) => t.tag === name);

    return entry ? new DataView(sfnt.buffer, sfnt.byteOffset + entry.offset, entry.length) : null;
}

const utf16 = new TextDecoder('utf-16be');
const latin1 = new TextDecoder('latin1');

/** Reads the name table, preferring the English Windows names that most fonts carry. */
function names(sfnt) {
    const view = table(sfnt, 'name');
    const found = {};

    if (!view) {
        return found;
    }

    const count = view.getUint16(2);
    const storage = view.getUint16(4);
    const rank = (platform, language) => (platform === 3 ? (language === 0x409 ? 3 : 2) : platform === 1 && language === 0 ? 1 : 0);

    for (let i = 0; i < count; i++) {
        const record = 6 + i * 12;
        const platform = view.getUint16(record);
        const language = view.getUint16(record + 4);
        const id = view.getUint16(record + 6);
        const length = view.getUint16(record + 8);
        const offset = view.getUint16(record + 10);
        const score = rank(platform, language);

        if (score === 0 || (found[id] && found[id].score >= score)) {
            continue;
        }

        const bytes = new Uint8Array(view.buffer, view.byteOffset + storage + offset, length);
        found[id] = { score, value: (platform === 3 ? utf16 : latin1).decode(bytes).trim() };
    }

    return Object.fromEntries(Object.entries(found).map(([id, { value }]) => [id, value]));
}

/** The weight range of a variable font's wght axis, if it has one. */
function weightAxis(sfnt) {
    const view = table(sfnt, 'fvar');

    if (!view) {
        return null;
    }

    const axesOffset = view.getUint16(4);
    const count = view.getUint16(8);
    const size = view.getUint16(10);

    for (let i = 0; i < count; i++) {
        const axis = axesOffset + i * size;

        if (tag(view, axis) === 'wght') {
            return [view.getInt32(axis + 4) / 65536, view.getInt32(axis + 12) / 65536].map(Math.round);
        }
    }

    return null;
}

/** What the @font-face rule needs to know about a font: family, weight and style. */
export function describe(sfnt) {
    const name = names(sfnt);
    const os2 = table(sfnt, 'OS/2');
    const range = weightAxis(sfnt);
    const subfamily = name[17] || name[2] || 'Regular';
    const selection = os2 ? os2.getUint16(62) : 0;

    let style = 'normal';
    if (selection & 0x200 || /oblique/i.test(subfamily)) {
        style = 'oblique';
    } else if (selection & 0x1 || /italic/i.test(subfamily)) {
        style = 'italic';
    }

    return {
        family: name[16] || name[1] || 'Untitled',
        subfamily,
        weight: range ? (range[0] === range[1] ? `${range[0]}` : `${range[0]} ${range[1]}`) : `${os2 ? os2.getUint16(4) : 400}`,
        style,
        variable: Boolean(table(sfnt, 'fvar')),
    };
}
