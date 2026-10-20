# wuff.web

Wuff turns TTF and OTF fonts into WOFF2 and WOFF, right in the browser, and writes the `@font-face` CSS for them. Live at [wuff.kimiyu.ch](https://wuff.kimiyu.ch).

A single-page Laravel 13 site styled with Tailwind CSS 4, built like [croc.web](https://github.com/kimiyuch/croc.web). Laravel only serves the page; the converting happens in the browser and fonts are never uploaded.

## Local development

The site runs in [Herd](https://herd.laravel.com) at `https://wuff.web.test`.

```sh
composer install
npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
npm run dev       # or: npm run build
```

## Deploying

```sh
npm run build     # if you changed the design or the converter; commit public/build
./deploy.sh
```

`deploy.sh` runs the tests, exports the committed code, installs production dependencies and syncs it to Hostpoint over SSH with your own key. The server's `.env`, `public/.htaccess` (the PHP version is set there through the Hostpoint panel) and `storage/` are left alone.

## How the converter works

| | |
|---|---|
| `resources/js/fonts/convert.js` | Unpacks a dropped file (TTF, OTF, WOFF or WOFF2) and makes each format; writes the `@font-face` rules |
| `resources/js/fonts/woff.js` | WOFF 1.0 in both directions, with the browser's `CompressionStream` for zlib |
| `resources/js/fonts/sfnt.js` | Reads the table directory, and the family, weight and style for the CSS |
| `resources/js/fonts/zip.js` | A small ZIP writer for “Download all” |
| `resources/js/app.js` | The drop zone, the list of fonts and the CSS panel |

WOFF2 comes from [woff2-encoder](https://github.com/itskyedo/woff2-encoder): Google's [woff2](https://github.com/google/woff2) compiled to WebAssembly. It's about 1 MB, so it's loaded in its own chunk as soon as someone points at the converter.

## Where things are

| | |
|---|---|
| `resources/views/home.blade.php` | The landing page |
| `resources/views/components/converter.blade.php` | The converter's markup, with templates for the rows |
| `resources/views/components/layouts/app.blade.php` | The HTML shell, fonts and meta tags |
| `resources/css/app.css` | Theme colors (`ochre`, the red earth and the dingo's ginger) and fonts |
| `config/wuff.php` | The repository link |
| `artwork/` | The icon and mascot as SVG; `scripts/icons.sh` renders them to `public/images` |
| `scripts/og-image.sh` | Renders the Open Graph image from `/og-image` |
