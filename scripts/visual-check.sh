#!/usr/bin/env bash
# Compares kit pages with a baseline taken before a change:
#   scripts/visual-check.sh <baseline-dir> <page…>
# Needs the playground served on :8321 (cd playground && php artisan serve --port=8321).
# Rebuilds the playground's CSS and takes the screenshots under one lock, so
# several checks can run at once without catching each other's half-built CSS.
set -euo pipefail
cd "$(dirname "$0")/.."
base="$1"; shift
run="${SIGTA_VISUAL_DIR:-${TMPDIR:-/tmp}}/sigta-visual-$$"
mkdir -p "$run/before" "$run/after"
for page in "$@"; do cp "$base/$page@"*.png "$run/before/"; done
(
    flock 9
    node packages/contract/scripts/build.ts >/dev/null 2>&1
    (cd playground && npm run build >/dev/null 2>&1)
    TMPDIR=/tmp node scripts/visual-snapshot.js "$run/after" http://127.0.0.1:8321 "$@" >/dev/null
) 9>/tmp/sigta-playground.lock
node scripts/visual-compare.js "$run/before" "$run/after" "$run/diff" && rm -rf "$run" || { echo "diffs: $run/diff"; exit 1; }
