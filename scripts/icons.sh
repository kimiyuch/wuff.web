#!/bin/zsh
# Renders the app icon in artwork/ to the PNG and WebP files the site uses, with headless Chrome and ImageMagick.
set -euo pipefail
cd "$(dirname "$0")/.."
CHROME="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
TMP=$(mktemp -d)
trap 'rm -rf "$TMP"' EXIT

render() {
  "$CHROME" --headless --disable-gpu --hide-scrollbars --force-device-scale-factor=1 \
    --default-background-color=00000000 --window-size=1024,1024 \
    --screenshot="$TMP/$2.png" "file://$PWD/artwork/$1.svg" 2>/dev/null
}

render icon icon
render icon-square square
render mascot mascot

cp "$TMP/icon.png" public/images/wuff.png
magick "$TMP/icon.png" -resize 384x384 -quality 90 public/images/wuff.webp
magick "$TMP/icon.png" -resize 64x64 public/images/favicon.png
magick "$TMP/square.png" -resize 256x256 public/apple-touch-icon.png
echo "Wrote public/images/wuff.png, wuff.webp, favicon.png and public/apple-touch-icon.png"
