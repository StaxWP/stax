#!/bin/sh
# Builds dist/stax-<version>.zip for WordPress.org from the current checkout.
# Runs the asset build first, then copies everything not listed in .distignore.
set -e
cd "$(dirname "$0")/.."
VERSION=$(sed -n 's/^Version: *//p' style.css | tr -d '\r')
[ -d node_modules ] || npm install --silent
npm run --silent build >/dev/null
rm -rf dist && mkdir -p dist/stax
rsync -a --exclude-from=.distignore ./ dist/stax/
(cd dist && zip -qr "stax-$VERSION.zip" stax)
echo "dist/stax-$VERSION.zip ($(du -h "dist/stax-$VERSION.zip" | cut -f1), $(find dist/stax -type f | wc -l | tr -d ' ') files)"
