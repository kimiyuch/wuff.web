@php($repository = config('wuff.repository_url'))

<footer class="border-t border-neutral-950/5">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-6 py-10 sm:flex-row sm:items-center sm:justify-between lg:px-8">
        <p class="text-sm/6 text-neutral-600">Wuff, made with <span aria-label="love">❤️</span> by <a href="https://kimiyu.ch" class="font-medium text-ochre-800 hover:text-ochre-600">kimiyu.ch</a></p>
        <nav class="flex flex-wrap gap-x-6 gap-y-2 whitespace-nowrap">
            <a href="{{ $repository }}" class="text-sm/6 font-normal text-neutral-600 hover:text-ochre-700">GitHub</a>
            <a href="{{ $repository }}/issues" class="text-sm/6 font-normal text-neutral-600 hover:text-ochre-700">Report a problem</a>
            <a href="/legal" class="text-sm/6 font-normal text-neutral-600 hover:text-ochre-700">Legal</a>
        </nav>
    </div>
</footer>
