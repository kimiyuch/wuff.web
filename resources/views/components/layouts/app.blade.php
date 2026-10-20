<!DOCTYPE html>
<html lang="en" class="bg-white antialiased motion-safe:scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Wuff: turn TTF and OTF fonts into WOFF2 and WOFF' }}</title>
    <meta name="description" content="Wuff turns TTF and OTF fonts into WOFF2 and WOFF web fonts, right in your browser, and writes the @font-face CSS for you. Your fonts are never uploaded.">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Wuff">
    <meta property="og:title" content="Wuff: turn TTF and OTF fonts into WOFF2 and WOFF">
    <meta property="og:description" content="Drop in a font or a whole family and take home small WOFF2 and WOFF files and the CSS to use them. Converted in your browser, never uploaded.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="The Wuff app icon next to the words: Fetch your fonts for the web.">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-neutral-950">
    <div class="isolate">
        {{ $slot }}
    </div>
</body>
</html>
