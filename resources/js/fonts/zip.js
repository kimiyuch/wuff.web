// A minimal ZIP writer for "Download all". Web fonts are already compressed, so the files are
// stored as they are (method 0) and the archive only needs a CRC-32 for each.

const crcTable = Array.from({ length: 256 }, (_, n) => {
    let c = n;
    for (let k = 0; k < 8; k++) {
        c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1;
    }
    return c >>> 0;
});

function crc32(bytes) {
    let crc = 0xffffffff;
    for (const byte of bytes) {
        crc = crcTable[(crc ^ byte) & 0xff] ^ (crc >>> 8);
    }
    return (crc ^ 0xffffffff) >>> 0;
}

/** Builds a ZIP file from a list of { name, bytes }; returns a Blob. */
export function zip(files) {
    const encoder = new TextEncoder();
    const now = new Date();
    const time = (now.getHours() << 11) | (now.getMinutes() << 5) | (now.getSeconds() >> 1);
    const date = ((now.getFullYear() - 1980) << 9) | ((now.getMonth() + 1) << 5) | now.getDate();

    const parts = [];
    const directory = [];
    let offset = 0;

    for (const file of files) {
        const name = encoder.encode(file.name);
        const crc = crc32(file.bytes);
        const local = new DataView(new ArrayBuffer(30));
        const central = new DataView(new ArrayBuffer(46));

        // Local file header; flag bit 11 marks the name as UTF-8.
        local.setUint32(0, 0x04034b50, true);
        local.setUint16(4, 20, true);
        local.setUint16(6, 0x0800, true);
        local.setUint16(10, time, true);
        local.setUint16(12, date, true);
        local.setUint32(14, crc, true);
        local.setUint32(18, file.bytes.length, true);
        local.setUint32(22, file.bytes.length, true);
        local.setUint16(26, name.length, true);

        central.setUint32(0, 0x02014b50, true);
        central.setUint16(4, 20, true);
        central.setUint16(6, 20, true);
        central.setUint16(8, 0x0800, true);
        central.setUint16(12, time, true);
        central.setUint16(14, date, true);
        central.setUint32(16, crc, true);
        central.setUint32(20, file.bytes.length, true);
        central.setUint32(24, file.bytes.length, true);
        central.setUint16(28, name.length, true);
        central.setUint32(42, offset, true);

        parts.push(local, name, file.bytes);
        directory.push(central, name);
        offset += 30 + name.length + file.bytes.length;
    }

    const size = directory.reduce((total, part) => total + part.byteLength, 0);
    const end = new DataView(new ArrayBuffer(22));
    end.setUint32(0, 0x06054b50, true);
    end.setUint16(8, files.length, true);
    end.setUint16(10, files.length, true);
    end.setUint32(12, size, true);
    end.setUint32(16, offset, true);

    return new Blob([...parts, ...directory, end], { type: 'application/zip' });
}
