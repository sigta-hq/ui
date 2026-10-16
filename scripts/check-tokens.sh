#!/usr/bin/env bash
# Token guard: fail if raw hex colors appear outside the theme layer.
# Components must reference tokens (var(--sigta-*)) or Tailwind theme
# utilities — never hex. Theme files are the ONLY place literal colors live.
# Built output (packages/*/dist, dist/) is generated and not scanned.
set -euo pipefail
cd "$(dirname "$0")/.."

paths=(resources/views resources/js resources/css src packages/contract/css packages/core/src)
[[ -d packages/svelte/src ]] && paths+=(packages/svelte/src)

matches=$(grep -rInE '#[0-9a-fA-F]{6}([0-9a-fA-F]{2})?\b|#[0-9a-fA-F]{3}\b' \
    "${paths[@]}" \
    --include='*.blade.php' --include='*.php' --include='*.js' --include='*.ts' --include='*.css' --include='*.svelte' \
    | grep -vE '^packages/contract/css/(theme\.css|theme\.example\.css):' || true)

if [[ -n "$matches" ]]; then
    echo "Raw hex colors found — use tokens (var(--sigta-*)) instead:"
    echo "$matches"
    exit 1
fi

echo "Token guard: clean."
