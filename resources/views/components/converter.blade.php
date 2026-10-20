{{--
    The converter: format switches, the drop zone, the list of fonts and the CSS for them.
    resources/js/app.js brings it to life; everything happens in the browser and nothing is uploaded.
--}}
<div {{ $attributes->class('text-left') }} data-converter>
    <div id="convert" class="group/converter scroll-mt-8 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-ochre-950/10" data-panel>
        {{-- The dark band at the top, like the windows of the other kimiyu apps --}}
        <div class="flex flex-wrap items-center gap-x-3 gap-y-4 border-b border-white/10 bg-linear-to-b from-ochre-800 to-ochre-950 px-4 py-4 sm:px-6">
            <img src="{{ asset('images/wuff.webp') }}" alt="" class="size-9">
            <div class="min-w-0">
                <div class="font-display text-base font-semibold text-white">Wuff</div>
                <div class="truncate text-sm/5 text-ochre-100/70 tabular-nums" data-status aria-live="polite">Drop fonts to start</div>
            </div>

            <fieldset class="ml-auto flex items-center gap-x-2">
                <legend class="sr-only">Formats to make</legend>
                @foreach (['woff2' => 'WOFF2', 'woff' => 'WOFF'] as $value => $label)
                    <label class="inline-flex cursor-pointer items-center gap-x-1.5 rounded-full bg-white/5 px-3 py-1 text-sm/6 font-semibold text-ochre-100/70 ring-1 ring-white/10 select-none hover:bg-white/10 has-checked:bg-ochre-300 has-checked:text-ochre-950 has-checked:ring-ochre-300 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-ochre-300">
                        <input type="checkbox" name="format" value="{{ $value }}" checked class="peer sr-only">
                        <x-heroicon-m-check class="hidden size-4 shrink-0 fill-current peer-checked:block" />
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>
        </div>

        {{-- Drop zone; the whole page takes drops too --}}
        <label for="font-files" data-dropzone class="m-4 flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-ochre-200 bg-ochre-50/40 px-6 py-14 text-center transition-colors group-data-filled/converter:flex-row group-data-filled/converter:gap-x-3 group-data-filled/converter:py-5 hover:border-ochre-300 hover:bg-ochre-50 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-ochre-600 data-dragging:border-ochre-500 data-dragging:bg-ochre-100/60 sm:m-6">
            <x-heroicon-o-arrow-up-tray class="size-10 shrink-0 stroke-ochre-400 group-data-filled/converter:size-6" />
            <div class="mt-4 group-data-filled/converter:mt-0 group-data-filled/converter:text-left">
                <p class="text-base/7 font-semibold text-ochre-950">
                    <span class="group-data-filled/converter:hidden">Drop TTF or OTF fonts here</span>
                    <span class="hidden group-data-filled/converter:inline">Drop more fonts</span>
                </p>
                <p class="text-sm/6 text-pretty text-neutral-600">
                    Or <span class="font-semibold text-ochre-700 underline decoration-ochre-300 underline-offset-4">choose files</span>.
                    <span class="group-data-filled/converter:hidden">A whole folder works too, and so do WOFF and WOFF2 files.</span>
                </p>
            </div>
            <input id="font-files" type="file" multiple accept=".ttf,.otf,.woff,.woff2,font/ttf,font/otf,font/woff,font/woff2" class="sr-only">
        </label>

        <ul role="list" class="divide-y divide-neutral-950/5 border-t border-neutral-950/5 group-not-data-filled/converter:hidden" data-list></ul>

        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-neutral-950/5 bg-ochre-50/60 px-4 py-4 group-not-data-filled/converter:hidden sm:px-6">
            <p class="text-sm/6 text-neutral-600 tabular-nums" data-summary></p>
            <div class="flex items-center gap-x-3">
                <button type="button" data-clear class="rounded-full bg-white px-4 py-2 text-sm/6 font-semibold text-ochre-800 ring-1 ring-ochre-950/10 hover:bg-ochre-50">Clear</button>
                <button type="button" data-download-all class="inline-flex items-center gap-x-2 rounded-full bg-ochre-600 py-2 pr-4 pl-3 text-sm/6 font-semibold text-white shadow-sm hover:bg-ochre-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ochre-600 disabled:opacity-50">
                    <x-heroicon-m-arrow-down-tray class="size-5 shrink-0 fill-white" />
                    Download all <span class="font-normal text-ochre-100">.zip</span>
                </button>
            </div>
        </div>
    </div>

    {{-- The @font-face rules for everything in the list --}}
    <div class="mt-8 overflow-hidden rounded-xl bg-ochre-950 ring-1 ring-white/10" data-css-panel hidden>
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 px-4 py-4 sm:px-6">
            <div>
                <h2 class="font-display text-base font-semibold text-white">Your CSS</h2>
                <p class="text-sm/6 text-pretty text-ochre-100/70">Put the font files next to your stylesheet, or change the paths in <code class="font-mono text-ochre-200">url()</code>.</p>
            </div>
            <button type="button" data-copy class="inline-flex items-center gap-x-1.5 rounded-full bg-white/5 px-3 py-1 text-sm/6 font-semibold text-ochre-200 ring-1 ring-white/10 hover:bg-white/10">
                <x-heroicon-m-clipboard-document class="size-4 shrink-0 fill-current" />
                <span data-copy-label>Copy</span>
            </button>
        </div>
        <pre class="max-h-96 overflow-auto p-4 font-mono text-xs/5 text-ochre-100 sm:px-6"><code data-css></code></pre>
    </div>

    <template data-row-template>
        <li class="flex flex-wrap items-center gap-x-4 gap-y-3 px-4 py-4 sm:flex-nowrap sm:px-6">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-ochre-50 text-2xl text-ochre-950 ring-1 ring-ochre-950/5" data-preview aria-hidden="true">Aa</div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm/6 font-semibold text-ochre-950" data-name></p>
                <p class="text-sm/6 text-neutral-500 tabular-nums data-error:text-red-600 sm:truncate" data-detail></p>
            </div>
            <div class="flex flex-wrap items-center gap-2 max-sm:order-last max-sm:grid max-sm:w-full max-sm:grid-cols-2" data-outputs>
                <span class="inline-flex items-center gap-x-2 text-sm/6 text-neutral-500" data-working>
                    <svg class="size-4 animate-spin text-ochre-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    Converting…
                </span>
            </div>
            <button type="button" data-remove class="-m-2 rounded-full p-2 text-neutral-400 hover:text-ochre-700 sm:ml-1">
                <span class="sr-only">Remove</span>
                <x-heroicon-m-x-mark class="size-5 fill-current" />
            </button>
        </li>
    </template>

    <template data-output-template>
        <a class="inline-flex items-center justify-center gap-x-1.5 rounded-full bg-white px-3 py-1 text-sm/6 font-semibold whitespace-nowrap text-ochre-800 ring-1 ring-ochre-950/10 hover:bg-ochre-50">
            <x-heroicon-m-arrow-down-tray class="size-4 shrink-0 fill-ochre-500" />
            <span data-format></span>
            <span class="font-normal text-neutral-500 tabular-nums" data-size></span>
        </a>
    </template>
</div>
