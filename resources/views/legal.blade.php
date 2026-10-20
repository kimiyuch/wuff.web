@php($repository = config('wuff.repository_url'))

<x-layouts.app title="Legal Notice · Wuff">
    <div class="absolute inset-x-0 top-0 -z-10 h-[44rem] bg-linear-to-b from-ochre-50 to-white"></div>

    <x-site-header />

    <main class="mx-auto max-w-6xl px-6 pt-10 pb-24 sm:pt-16 sm:pb-32 lg:px-8">
        <p class="text-base/7 font-semibold text-ochre-600">Legal notice</p>
        <h1 class="mt-2 max-w-[30ch] font-display text-4xl font-semibold tracking-tight text-balance text-ochre-950 sm:text-5xl">
            The small print
        </h1>
        <p class="mt-6 max-w-[48ch] text-lg/8 text-pretty text-neutral-600">
            Wuff is a free tool that converts fonts in your browser. Here’s what that means for you, and what it doesn’t.
        </p>

        <div class="mt-16 divide-y divide-neutral-950/5 border-t border-neutral-950/5">
            <section id="fonts" class="grid scroll-mt-8 grid-cols-1 gap-x-12 gap-y-4 py-12 sm:grid-cols-[12rem_1fr]">
                <h2 class="font-display text-2xl font-semibold tracking-tight text-ochre-950">Your fonts</h2>
                <div class="max-w-[65ch] space-y-4 text-base/7 text-pretty text-neutral-600">
                    <p>
                        Wuff converts your fonts on your own computer, in your browser. The fonts and the files made from
                        them are never uploaded to us or anyone else, and your original files aren’t changed.
                    </p>
                    <p>
                        Converting a font doesn’t change what you’re allowed to do with it. Many font licences treat use on
                        websites separately from use on your computer, so check the licence before you put a font online.
                    </p>
                </div>
            </section>

            <section id="warranty" class="grid scroll-mt-8 grid-cols-1 gap-x-12 gap-y-4 py-12 sm:grid-cols-[12rem_1fr]">
                <h2 class="font-display text-2xl font-semibold tracking-tight text-ochre-950">No warranty</h2>
                <div class="max-w-[65ch] space-y-4 text-base/7 text-pretty text-neutral-600">
                    <p>
                        Wuff is provided free of charge and “as is”, without any warranty, under the
                        <a href="{{ $repository }}/blob/main/LICENSE" class="font-medium text-ochre-800 hover:text-ochre-600">MIT License</a>.
                        It includes <a href="https://github.com/google/woff2" class="font-medium text-ochre-800 hover:text-ochre-600">woff2</a>
                        and <a href="https://github.com/google/brotli" class="font-medium text-ochre-800 hover:text-ochre-600">Brotli</a> by Google,
                        through <a href="https://github.com/itskyedo/woff2-encoder" class="font-medium text-ochre-800 hover:text-ochre-600">woff2-encoder</a>,
                        all under the MIT License. To the extent the law allows, we’re not liable for any damage or loss from using it.
                    </p>
                </div>
            </section>

            <section id="privacy" class="grid scroll-mt-8 grid-cols-1 gap-x-12 gap-y-4 py-12 sm:grid-cols-[12rem_1fr]">
                <h2 class="font-display text-2xl font-semibold tracking-tight text-ochre-950">Privacy</h2>
                <div class="max-w-[65ch] space-y-4 text-base/7 text-pretty text-neutral-600">
                    <p>
                        Wuff has no accounts, ads or analytics. The fonts you convert stay on your computer. Wuff only
                        remembers which formats you picked, in your browser’s local storage.
                    </p>
                    <p>
                        This website doesn’t use tracking and loads nothing from other servers. It sets a session cookie that
                        the website framework needs to work, and our host keeps the usual server logs.
                    </p>
                </div>
            </section>

            <section id="trademarks" class="grid scroll-mt-8 grid-cols-1 gap-x-12 gap-y-4 py-12 sm:grid-cols-[12rem_1fr]">
                <h2 class="font-display text-2xl font-semibold tracking-tight text-ochre-950">Trademarks</h2>
                <div class="max-w-[65ch] space-y-4 text-base/7 text-pretty text-neutral-600">
                    <p>
                        TrueType is a trademark of Apple Inc. OpenType is a trademark of Microsoft Corporation. Google is a
                        trademark of Google LLC. Wuff isn’t affiliated with or endorsed by any of them.
                    </p>
                </div>
            </section>

            <section id="contact" class="grid scroll-mt-8 grid-cols-1 gap-x-12 gap-y-4 py-12 sm:grid-cols-[12rem_1fr]">
                <h2 class="font-display text-2xl font-semibold tracking-tight text-ochre-950">Contact</h2>
                <div class="max-w-[65ch] space-y-4 text-base/7 text-pretty text-neutral-600">
                    <p>
                        Wuff is made by <a href="https://kimiyu.ch" class="font-medium text-ochre-800 hover:text-ochre-600">kimiyu.ch</a>.
                        For questions or problems with a font, please
                        <a href="{{ $repository }}/issues" class="font-medium text-ochre-800 hover:text-ochre-600">open an issue on GitHub</a>.
                    </p>
                </div>
            </section>
        </div>
    </main>

    <x-site-footer />
</x-layouts.app>
