import { FontError, convert, css, loadWoff2, mimeType } from './fonts/convert.js';
import { zip } from './fonts/zip.js';

const FORMATS = ['woff2', 'woff'];
const LABELS = { woff2: 'WOFF2', woff: 'WOFF' };
const EXTENSIONS = /\.(ttf|otf|woff2?)$/i;

function size(bytes) {
    if (bytes < 1024) {
        return `${bytes} B`;
    }
    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(bytes < 10 * 1024 ? 1 : 0)} KB`;
    }
    return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}

function smaller(from, to) {
    const percent = Math.round((1 - to / from) * 100);

    return percent > 0 ? `${percent}% smaller` : 'same size';
}

/** Reads the files from a drop, walking into dropped folders and keeping only font files from them. */
async function droppedFiles(dataTransfer) {
    const entries = [...dataTransfer.items].map((item) => item.webkitGetAsEntry?.()).filter(Boolean);

    if (!entries.length) {
        return [...dataTransfer.files];
    }

    const files = [];
    const walk = async (entry, inFolder) => {
        if (entry.isFile) {
            const file = await new Promise((resolve, reject) => entry.file(resolve, reject));
            if (!inFolder || EXTENSIONS.test(file.name)) {
                files.push(file);
            }
        } else if (entry.isDirectory) {
            const reader = entry.createReader();
            let batch;
            // readEntries returns at most 100 entries at a time.
            while ((batch = await new Promise((resolve, reject) => reader.readEntries(resolve, reject))).length) {
                for (const child of batch) {
                    await walk(child, true);
                }
            }
        }
    };

    for (const entry of entries) {
        await walk(entry, false);
    }

    return files;
}

function converter(root) {
    const panel = root.querySelector('[data-panel]');
    const dropzone = root.querySelector('[data-dropzone]');
    const input = root.querySelector('#font-files');
    const list = root.querySelector('[data-list]');
    const status = root.querySelector('[data-status]');
    const summary = root.querySelector('[data-summary]');
    const downloadAll = root.querySelector('[data-download-all]');
    const cssPanel = root.querySelector('[data-css-panel]');
    const cssCode = root.querySelector('[data-css]');
    const copyLabel = root.querySelector('[data-copy-label]');
    const rowTemplate = root.querySelector('[data-row-template]');
    const outputTemplate = root.querySelector('[data-output-template]');
    const switches = [...root.querySelectorAll('input[name="format"]')];

    /** Every dropped file: { id, file, base, row, state: 'working' | 'done' | 'error', result, urls } */
    let fonts = [];
    let nextId = 1;

    // Both formats are always made, so the switches only change what's shown, zipped and in the CSS.
    const saved = JSON.parse(localStorage.getItem('wuff.formats') ?? 'null');
    if (Array.isArray(saved) && saved.some((format) => FORMATS.includes(format))) {
        switches.forEach((box) => (box.checked = saved.includes(box.value)));
    }
    const formats = () => switches.filter((box) => box.checked).map((box) => box.value);

    switches.forEach((box) =>
        box.addEventListener('change', () => {
            // One format has to stay on.
            if (!formats().length) {
                box.checked = true;
            }
            localStorage.setItem('wuff.formats', JSON.stringify(formats()));
            fonts.forEach(renderOutputs);
            update();
        }),
    );

    // Start loading the WOFF2 encoder as soon as someone looks like they're about to use it.
    ['pointerenter', 'focusin', 'dragenter'].forEach((type) => panel.addEventListener(type, () => loadWoff2().catch(() => {}), { once: true }));

    input.addEventListener('change', () => {
        add([...input.files]);
        input.value = '';
    });

    // Drops anywhere on the page land in the converter, instead of the browser opening the font.
    let depth = 0;
    const hasFiles = (event) => event.dataTransfer?.types.includes('Files');

    window.addEventListener('dragenter', (event) => {
        if (hasFiles(event)) {
            depth++;
            dropzone.dataset.dragging = '';
        }
    });
    window.addEventListener('dragleave', (event) => {
        if (hasFiles(event) && --depth <= 0) {
            depth = 0;
            delete dropzone.dataset.dragging;
        }
    });
    window.addEventListener('dragover', (event) => {
        if (hasFiles(event)) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'copy';
        }
    });
    window.addEventListener('drop', async (event) => {
        if (!hasFiles(event)) {
            return;
        }
        event.preventDefault();
        depth = 0;
        delete dropzone.dataset.dragging;
        add(await droppedFiles(event.dataTransfer));
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    root.querySelector('[data-clear]').addEventListener('click', () => {
        fonts.forEach(forget);
        fonts = [];
        list.replaceChildren();
        update();
        input.focus();
    });

    downloadAll.addEventListener('click', () => {
        const done = fonts.filter((font) => font.state === 'done');
        const taken = new Set();
        const files = [];

        for (const font of done) {
            for (const format of formats()) {
                let name = `${font.base}.${format}`;
                for (let n = 2; taken.has(name.toLowerCase()); n++) {
                    name = `${font.base}-${n}.${format}`;
                }
                taken.add(name.toLowerCase());
                files.push({ name, bytes: font.result.outputs[format] });
            }
        }
        files.push({ name: 'fonts.css', bytes: new TextEncoder().encode(stylesheet() + '\n') });

        const families = [...new Set(done.map((font) => font.result.info.family))];
        save(zip(files), `${families.length === 1 ? families[0] : 'fonts'}-web.zip`);
    });

    root.querySelector('[data-copy]').addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(cssCode.textContent);
            copyLabel.textContent = 'Copied';
        } catch {
            // Clipboard blocked: select the text so ⌘C works.
            getSelection().selectAllChildren(cssCode);
            copyLabel.textContent = 'Press ⌘C';
        }
        setTimeout(() => (copyLabel.textContent = 'Copy'), 2000);
    });

    function save(blob, name) {
        const url = URL.createObjectURL(blob);
        const link = Object.assign(document.createElement('a'), { href: url, download: name });
        link.click();
        setTimeout(() => URL.revokeObjectURL(url), 10_000);
    }

    async function add(files) {
        const added = files.map((file) => {
            const font = { id: nextId++, file, base: file.name.replace(/\.[^.]+$/, '') || file.name, state: 'working', urls: {} };
            font.row = rowTemplate.content.firstElementChild.cloneNode(true);
            font.row.querySelector('[data-name]').textContent = file.name;
            font.row.querySelector('[data-detail]').textContent = size(file.size);
            font.row.querySelector('[data-remove]').addEventListener('click', () => remove(font));
            list.append(font.row);
            return font;
        });

        fonts.push(...added);
        update();

        // One after the other keeps the page responsive with a big family.
        for (const font of added) {
            try {
                font.result = await convert(new Uint8Array(await font.file.arrayBuffer()), FORMATS);
                font.state = 'done';
                await preview(font);
            } catch (error) {
                font.state = 'error';
                font.error = error instanceof FontError ? error.message : 'Wuff couldn’t read this font. It may be damaged.';
                if (!(error instanceof FontError)) {
                    console.error(error);
                }
            }

            if (fonts.includes(font)) {
                render(font);
                update();
            }
        }
    }

    // The "Aa" in each row is set in the font itself.
    async function preview(font) {
        try {
            const face = new FontFace(`wuff-preview-${font.id}`, font.result.sfnt);
            document.fonts.add(await face.load());
            font.face = face;
        } catch {
            // The browser can't show it; the plain "Aa" stays.
        }
    }

    function render(font) {
        const name = font.row.querySelector('[data-name]');
        const detail = font.row.querySelector('[data-detail]');

        if (font.state === 'error') {
            detail.dataset.error = '';
            detail.textContent = font.error;
            font.row.querySelector('[data-outputs]').replaceChildren();
            return;
        }

        const { info } = font.result;
        const style = info.variable ? 'Variable' : info.subfamily;
        name.textContent = info.family.endsWith(style) ? info.family : `${info.family} ${style}`;
        name.title = font.file.name;
        if (font.face) {
            font.row.querySelector('[data-preview]').style.fontFamily = `"${font.face.family}"`;
        }
        renderOutputs(font);
    }

    function renderOutputs(font) {
        if (font.state !== 'done') {
            return;
        }

        const chosen = formats();
        const detail = font.row.querySelector('[data-detail]');
        const smallest = Math.min(...chosen.map((format) => font.result.outputs[format].length));
        detail.textContent = `${font.file.name} · ${size(font.file.size)} → ${size(smallest)}, ${smaller(font.file.size, smallest)}`;

        font.row.querySelector('[data-outputs]').replaceChildren(
            ...chosen.map((format) => {
                const bytes = font.result.outputs[format];
                font.urls[format] ??= URL.createObjectURL(new Blob([bytes], { type: mimeType(format) }));

                const link = outputTemplate.content.firstElementChild.cloneNode(true);
                link.href = font.urls[format];
                link.download = `${font.base}.${format}`;
                link.querySelector('[data-format]').textContent = LABELS[format];
                link.querySelector('[data-size]').textContent = size(bytes.length);
                link.setAttribute('aria-label', `Download ${font.base}.${format}, ${size(bytes.length)}`);
                return link;
            }),
        );
    }

    function forget(font) {
        Object.values(font.urls).forEach((url) => URL.revokeObjectURL(url));
        if (font.face) {
            document.fonts.delete(font.face);
        }
    }

    function remove(font) {
        forget(font);
        fonts = fonts.filter((other) => other !== font);
        font.row.remove();
        update();
        if (!fonts.length) {
            input.focus();
        }
    }

    const stylesheet = () => css(fonts.filter((font) => font.state === 'done').map((font) => ({ info: font.result.info, base: font.base })), formats());

    function update() {
        const done = fonts.filter((font) => font.state === 'done');
        const working = fonts.filter((font) => font.state === 'working').length;
        const failed = fonts.filter((font) => font.state === 'error').length;

        panel.toggleAttribute('data-filled', fonts.length > 0);

        const count = (n, word) => `${n} ${word}${n === 1 ? '' : 's'}`;
        const parts = [count(fonts.length, failed ? 'file' : 'font')];
        if (working) parts.push(`${working} converting`);
        if (failed) parts.push(`${failed} didn’t work`);
        status.textContent = fonts.length ? parts.join(' · ') : 'Drop fonts to start';

        const before = done.reduce((total, font) => total + font.file.size, 0);
        const after = done.reduce((total, font) => total + Math.min(...formats().map((format) => font.result.outputs[format].length)), 0);
        summary.textContent = done.length ? `${size(before)} → ${size(after)} ${formats().includes('woff2') ? 'as WOFF2' : 'as WOFF'}, ${smaller(before, after)}` : '';

        downloadAll.disabled = !done.length || working > 0;
        cssPanel.hidden = !done.length;
        cssCode.textContent = stylesheet();
    }
}

const root = document.querySelector('[data-converter]');

if (root) {
    converter(root);
}
