@php
    $repository = config('wuff.repository_url');

    $features = [
        ['icon' => 'arrow-down-on-square-stack', 'title' => 'A whole family at once', 'body' => 'Drop every weight and style, or the folder they’re in. Wuff works through them one after the other.'],
        ['icon' => 'lock-closed', 'title' => 'Stays on your computer', 'body' => 'Fonts are converted right here in your browser and never uploaded, which matters for fonts you paid for.'],
        ['icon' => 'code-bracket', 'title' => 'The CSS, written for you', 'body' => 'Wuff reads each font’s family, weight and style and writes matching @font-face rules. Copy them or find them in the zip.'],
        ['icon' => 'adjustments-horizontal', 'title' => 'Variable fonts too', 'body' => 'Variable fonts keep all their axes, and the CSS gets the whole weight range, like font-weight: 100 900.'],
        ['icon' => 'archive-box-arrow-down', 'title' => 'One zip to take home', 'body' => 'Download each file on its own, or everything at once, with a fonts.css next to the fonts.'],
        ['icon' => 'check-badge', 'title' => 'The reference encoder', 'body' => 'WOFF2 files are made with woff2, Google’s reference encoder for the format, compiled to run in your browser.'],
        ['icon' => 'arrows-right-left', 'title' => 'Works both ways', 'body' => 'Only have a WOFF or a WOFF2? Drop it in, and Wuff makes the other format from it.'],
        ['icon' => 'speaker-x-mark', 'title' => 'Hardly ever barks', 'body' => 'Real dingoes rarely bark; they howl. Wuff is just as quiet: no sign-up, no ads, no tracking.'],
    ];

    $formats = [
        [
            'name' => 'WOFF2', 'tag' => 'Brotli', 'for' => 'For every website today',
            'body' => 'Every browser in use today reads it, and it’s a quarter to a third smaller than WOFF. If you serve only one format, serve this one.',
            'snippet' => "@font-face {\n  font-family: \"My Font\";\n  src: url(\"my-font.woff2\") format(\"woff2\");\n}",
        ],
        [
            'name' => 'WOFF', 'tag' => 'zlib', 'for' => 'For very old browsers',
            'body' => 'The first web font format. Only browsers from before 2016, like Internet Explorer 11, need it. List it after WOFF2 as a fallback.',
            'snippet' => "src: url(\"my-font.woff2\") format(\"woff2\"),\n     url(\"my-font.woff\") format(\"woff\");",
        ],
        [
            'name' => 'Preload', 'tag' => 'WOFF2', 'for' => 'For the font people see first',
            'body' => 'Tell the browser about your most important font early, so the headline shows up in it right away instead of switching after a moment.',
            'snippet' => '<link rel="preload" href="my-font.woff2" as="font" type="font/woff2" crossorigin>',
        ],
    ];
@endphp

<x-layouts.app>
    <div class="absolute inset-x-0 top-0 -z-10 h-[44rem] bg-linear-to-b from-ochre-50 to-white"></div>

    <x-site-header />

    <main>
        {{-- Hero, with the converter itself --}}
        <section class="relative pt-10 pb-24 sm:pt-16 sm:pb-32">
            <div class="mx-auto max-w-6xl px-6 text-center lg:px-8">
                <img src="{{ asset('images/wuff.webp') }}" width="384" height="384" fetchpriority="high" alt="The Wuff app icon: a dingo peeking over a card with the letters Aa" class="mx-auto size-32 sm:size-40">
                <h1 class="mx-auto mt-8 max-w-[16ch] font-display text-5xl font-semibold tracking-tight text-balance text-ochre-950 sm:text-7xl">
                    Fetch your fonts for the web
                </h1>
                <p class="mx-auto mt-6 max-w-[50ch] text-lg/8 text-pretty text-neutral-600 sm:text-xl/8">
                    Wuff turns TTF and OTF files into WOFF2 and WOFF, right here in your browser. Drop in a font or a whole family, and take home small web fonts and the CSS to use them. Your fonts never leave your computer.
                </p>

                <x-converter class="mx-auto mt-16 max-w-4xl sm:mt-20" />

                <p class="mx-auto mt-6 max-w-[60ch] text-base/7 text-pretty text-neutral-500 sm:text-sm/6">
                    Free, no sign-up. Works in Safari, Chrome, Firefox and Edge.
                    Only convert fonts you’re allowed to use on the web: many font licences treat that separately.
                </p>
            </div>
        </section>

        {{-- Formats --}}
        <section id="formats" class="scroll-mt-8 bg-ochre-950 py-24 sm:py-32">
            <div class="mx-auto max-w-6xl px-6 lg:px-8">
                <p class="text-base/7 font-semibold text-ochre-300">Formats</p>
                <h2 class="mt-2 max-w-[24ch] font-display text-4xl font-semibold tracking-tight text-balance text-white sm:text-5xl">
                    Small fonts, served right
                </h2>
                <p class="mt-6 max-w-[52ch] text-lg/8 text-pretty text-ochre-100/70">
                    A desktop font is often 100 to 300 KB. As WOFF2 it’s usually half that or less, with not a single curve changed. Here’s what to put on your website.
                </p>

                <div class="mt-16 grid grid-cols-1 gap-8 sm:mt-20 lg:grid-cols-3">
                    @foreach ($formats as $format)
                        <div class="flex flex-col rounded-2xl bg-white/5 p-8 ring-1 ring-white/10">
                            <div class="flex items-center gap-x-3">
                                <span class="text-base/7 font-semibold text-white">{{ $format['name'] }}</span>
                                <span class="rounded-full px-2 text-sm/6 font-medium text-ochre-300 ring-1 ring-ochre-300/50">{{ $format['tag'] }}</span>
                            </div>
                            <p class="mt-4 text-sm/6 font-semibold text-ochre-200">{{ $format['for'] }}</p>
                            <p class="mt-2 text-base/7 text-pretty text-ochre-100/70">{{ $format['body'] }}</p>
                            <pre class="mt-8 rounded-lg whitespace-pre-wrap [overflow-wrap:anywhere] bg-black/30 p-4 font-mono text-xs/5 text-ochre-100 ring-1 ring-white/5 sm:mt-auto"><code>{{ $format['snippet'] }}</code></pre>
                        </div>
                    @endforeach
                </div>

                <p class="mt-12 max-w-[60ch] text-base/7 text-pretty text-ochre-100/70">
                    Keep the original TTF and OTF files for your design apps. They work on websites too, but they’re two to three times bigger than WOFF2.
                </p>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="scroll-mt-8 py-24 sm:py-32">
            <div class="mx-auto max-w-6xl px-6 lg:px-8">
                <p class="text-base/7 font-semibold text-ochre-600">Features</p>
                <h2 class="mt-2 max-w-[30ch] font-display text-4xl font-semibold tracking-tight text-balance text-ochre-950 sm:text-5xl">
                    Everything you need, nothing you don’t
                </h2>
                <p class="mt-6 max-w-[48ch] text-lg/8 text-pretty text-neutral-600">
                    No settings to get wrong. Drop the fonts, pick the formats and download. Wuff remembers your choice for next time.
                </p>

                <dl class="mt-16 grid grid-cols-1 gap-x-12 gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($features as $feature)
                        <div>
                            <dt class="flex items-start gap-x-3 text-base/7 font-semibold text-ochre-950">
                                <x-dynamic-component :component="'heroicon-m-'.$feature['icon']" class="size-5 h-lh shrink-0 fill-ochre-500" />
                                {{ $feature['title'] }}
                            </dt>
                            <dd class="mt-1 pl-8 text-base/7 text-pretty text-neutral-600">{{ $feature['body'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- Under the hood --}}
        <section class="bg-ochre-50 py-24 sm:py-32">
            <div class="mx-auto grid max-w-6xl grid-cols-1 gap-x-12 gap-y-16 px-6 lg:grid-cols-2 lg:px-8">
                <div>
                    <p class="text-base/7 font-semibold text-ochre-600">Under the hood</p>
                    <h2 class="mt-2 max-w-[30ch] font-display text-4xl font-semibold tracking-tight text-balance text-ochre-950 sm:text-5xl">
                        Small, private, and runs in your browser
                    </h2>
                    <p class="mt-6 max-w-[48ch] text-lg/8 text-pretty text-neutral-600">
                        Wuff is a single web page. WOFF2 files are made by Google’s woff2 encoder, compiled to WebAssembly, and WOFF files by the compression built into your browser. Your fonts are read, converted and saved on your computer, and never sent anywhere.
                    </p>
                </div>
                <dl class="divide-y divide-ochre-950/10 self-center">
                    <div class="flex items-baseline justify-between gap-x-6 pb-6">
                        <dt class="text-base/7 text-neutral-600">Fonts uploaded</dt>
                        <dd class="font-display text-2xl font-semibold tracking-tight text-ochre-950">Never</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-x-6 py-6">
                        <dt class="text-base/7 text-neutral-600">Ads, accounts and analytics</dt>
                        <dd class="font-display text-2xl font-semibold tracking-tight text-ochre-950">None</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-x-6 py-6">
                        <dt class="text-base/7 text-neutral-600">Price</dt>
                        <dd class="font-display text-2xl font-semibold tracking-tight text-ochre-950">Free</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-x-6 py-6">
                        <dt class="text-base/7 text-neutral-600">Changes to your fonts’ shapes</dt>
                        <dd class="font-display text-2xl font-semibold tracking-tight text-ochre-950">None</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-x-6 pt-6">
                        <dt class="text-base/7 text-neutral-600">Source code</dt>
                        <dd class="font-display text-2xl font-semibold tracking-tight">
                            <a href="{{ $repository }}" class="text-ochre-700 underline decoration-ochre-300 underline-offset-4 hover:decoration-ochre-600">GitHub</a>
                        </dd>
                    </div>
                </dl>
            </div>
        </section>
    </main>

    <x-site-footer />
</x-layouts.app>
