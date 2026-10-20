{{-- Source for public/images/og.png (1200×630). Regenerate with scripts/og-image.sh. --}}
<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="utf-8">
    @fonts
    @vite(['resources/css/app.css'])
</head>
<body class="m-0">
    <div class="relative isolate flex h-[630px] w-[1200px] items-center gap-x-16 overflow-hidden bg-linear-to-br from-ochre-50 via-white to-ochre-100 px-24 font-sans">
        <img src="{{ asset('images/wuff.png') }}" alt="" class="size-[22rem] shrink-0">
        <div>
            <div class="font-display text-[7rem] font-semibold tracking-tight text-ochre-950">Wuff</div>
            <p class="mt-2 max-w-[20ch] font-display text-5xl font-medium tracking-tight text-balance text-ochre-800">
                Fetch your fonts for the web.
            </p>
            <p class="mt-8 text-2xl text-neutral-600">TTF and OTF to WOFF2 and WOFF, in your browser.</p>
            <p class="mt-2 text-2xl font-medium text-ochre-600">wuff.kimiyu.ch</p>
        </div>
    </div>
</body>
</html>
