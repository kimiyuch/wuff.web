// From a dropped file to web fonts: unpack it to TrueType/OpenType if needed, then make each format.
// The WOFF2 encoder is Google's woff2 compiled to WebAssembly (about 1 MB), so it's only loaded when needed.

import { describe, detect } from './sfnt.js';
import * as woff from './woff.js';

let woff2;
export const loadWoff2 = () => (woff2 ??= import('woff2-encoder'));

export class FontError extends Error {}

/** Turns a dropped file into the TrueType or OpenType data inside it. */
export async function unpack(bytes) {
    const kind = detect(bytes);

    switch (kind) {
        case 'ttf':
        case 'otf':
            return { kind, sfnt: bytes };
        case 'woff':
            return { kind, sfnt: await woff.decode(bytes) };
        case 'woff2':
            return { kind, sfnt: await (await loadWoff2()).decompress(bytes) };
        case 'ttc':
            throw new FontError('Font collections (.ttc) aren’t supported yet. Export the fonts one by one.');
        default:
            throw new FontError('This doesn’t look like a font file.');
    }
}

/** Converts a font file to the given formats ('woff2', 'woff'); returns its description and the new files. */
export async function convert(bytes, formats) {
    const { kind, sfnt } = await unpack(bytes);
    const info = describe(sfnt);
    const outputs = {};

    for (const format of formats) {
        outputs[format] = format === 'woff2' ? await (await loadWoff2()).compress(sfnt) : await woff.encode(sfnt);
    }

    return { kind, sfnt, info, outputs };
}

const MIME = { woff2: 'font/woff2', woff: 'font/woff' };
export const mimeType = (format) => MIME[format];

/** The @font-face rules for a set of converted fonts, sorted by family, weight and style. */
export function css(fonts, formats) {
    const order = ['woff2', 'woff'].filter((format) => formats.includes(format));

    return [...fonts]
        .sort((a, b) => a.info.family.localeCompare(b.info.family) || parseInt(a.info.weight) - parseInt(b.info.weight) || a.info.style.localeCompare(b.info.style))
        .map(({ info, base }) => {
            const sources = order.map((format) => `url("${base}.${format}") format("${format}")`).join(',\n       ');

            return [
                '@font-face {',
                `  font-family: "${info.family.replaceAll('"', '\\"')}";`,
                `  src: ${sources};`,
                `  font-weight: ${info.weight};`,
                `  font-style: ${info.style};`,
                '  font-display: swap;',
                '}',
            ].join('\n');
        })
        .join('\n\n');
}
