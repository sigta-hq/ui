## Goal

Make Sigta UI self-enforcing across LLM-built consumer apps. Three pieces, designed to travel *into every consumer project* and stay in sync automatically:

1. **Consumer-facing `AGENTS.md`** (via a side file `sigta-ui.md` it references) — the imperative rules an LLM coding agent reads
2. **A component reference doc** (`sigta-components.md`) — the exhaustive, scannable API list so LLMs don't traverse code
3. **A unified, idempotent `sigta:install` Artisan command** — one command publishes *and* updates everything (fonts + LLM context), runnable manually and via a composer hook on every update

## The design decisions (from clarification)

- **Side-file model:** the command owns `sigta-ui.md` (rules) + `sigta-components.md` (reference) at the consumer's project root, always overwriting both. It surgically injects **one** reference line into the consumer's `AGENTS.md` (only if absent) — it never clobbers existing `AGENTS.md` content. Works in projects that already have an `AGENTS.md` from other tools.
- **Unified command:** a single `sigta:install` does fonts + LLM context + prints next-steps. One composer hook = full sync on every update.
- **Custom Artisan command, not `vendor:publish`:** Laravel's `vendor:publish` can't do true idempotent updates (skips existing without `--force`, never prunes stale files with `--force`). A custom command gives overwrite-always for the context files + true mirror for fonts, with no `--force` footgun.

---

## Files to create

### 1. `stubs/sigta-ui.md` (new)
The consumer-facing LLM rules. Imperative voice, the prohibitions that matter:
- **Always** use `<sigta:button>`, `<sigta:input>`, `<sigta:select>`, `<sigta:textarea>`, `<sigta:field>`, etc. — **never** raw HTML form controls when a Sigta equivalent exists (explicit list)
- **Never** raw hex colors or Tailwind palette classes (`text-gray-500`, `bg-blue-600`) — use the token utilities only (`text-ink`, `text-graphite`, `bg-surface`, `bg-surface-raised`, `border-line`, `bg-accent`, etc.) — full utility list copied from CONVENTIONS.md
- **Never** import another icon library — use `<sigta:icon name="...">`
- **Always** import the Sigta stylesheet + `@source` in CSS; **always** add `@sigtaScripts` to layout head
- Fonts, shape utilities (`rounded-base/panel/frame`, `shadow-paper`), font families (`font-head/body/mono/hand`)
- Pointer to `sigta-components.md` for the full component API
- Short list of cross-component events (sigta-toast, sigta-modal-show, etc.)

This is the highest-leverage file. ~80 lines, dense and imperative.

### 2. `stubs/sigta-components.md` (new)
The exhaustive component reference — generated content from the exploration agent's findings. Every component: name, props with defaults, variants, one-line description. Grouped (typography, actions, forms, overlays, data, navigation, sections, etc.) using the existing `catalog.php` tier structure. This is the file LLMs scan to find the right component.

### 3. `src/Console/InstallCommand.php` (new)
The `sigta:install` Artisan command. Namespace `Sigta\Ui\Console` (PSR-4 from `src/`). Logic:
- **Fonts:** mirror `resources/fonts/` → `public_path('vendor/sigta/fonts')` — copy all, delete stale target files not in source (true idempotent update)
- **LLM context:** force-write `stubs/sigta-ui.md` → consumer root `sigta-ui.md`, same for `sigta-components.md` (always overwrite — these are package-owned, version-synced)
- **AGENTS.md reference line:** if consumer's `AGENTS.md` doesn't contain the marker line, append it (idempotent — only adds once). If `AGENTS.md` doesn't exist, create it with the reference line. Never modify any other content of `AGENTS.md`.
- **Output:** clear summary of what was published/updated, with next-steps (CSS @import reminder) for first-time installs
- Signature: `sigta:install` with optional `--only=fonts|agents` flag for granular runs (used internally by the composer hook if needed)

### 4. `src/SigtaServiceProvider.php` (edit)
Add a `register()` method calling `$this->commands([InstallCommand::class])`. Keeps the existing `boot()` untouched. This is the only change to the provider.

---

## Files to update (docs / sync notes)

### 5. `CONVENTIONS.md` (edit)
Add a new section **"LLM context files (keep in sync)"** near the top, after the existing structure. Imperative: whenever a component is added/removed or its `@props` change, regenerate `stubs/sigta-components.md`. Whenever a usage rule changes, update `stubs/sigta-ui.md`. Both ship with the package and are the single source of truth for LLM agents in consumer apps. Note that `sigta:install` overwrites them on the consumer side, so they must be accurate before release.

### 6. `README.md` (edit)
Add a new section **"LLM agent support"** documenting:
- Run `php artisan sigta:install` — publishes fonts + LLM context, idempotent (re-run to update)
- What it writes: `sigta-ui.md`, `sigta-components.md`, and a reference line in `AGENTS.md`
- How to auto-sync: add the composer hook (see #7)
- Why: consumer LLMs read these files instead of guessing

### 7. `playground/resources/views/kit/installation.blade.php` (edit)
Add a new section **"09 · LLM agent context"** covering:
- What `sigta:install` writes and why
- The command itself
- **The composer auto-run hook:** add `"post-autoload-dump": ["@php artisan sigta:install --ansi"]` (or extend existing) to consumer's `composer.json` so every `composer update` re-syncs the context. Show the snippet. Explain that because the command is idempotent, this is safe to run unconditionally.
- Caveat: the hook only runs in apps that have the package installed; first run still requires the VCS repo + auth from section 01–03

### 8. `playground/composer.json` (edit — demonstrate the hook in the playground itself)
Extend `post-autoload-dump` to also run `@php artisan sigta:install --ansi`. The playground is the living example, so it should dogfood the hook. (This also means the playground will pick up stub changes immediately during development — useful.)

---

## What the consumer experience looks like (end to end)

```bash
# One-time: wire the private repo + auth (existing install flow)
composer require sigta/ui:dev-main

# Then one command does everything:
php artisan sigta:install
# → fonts mirrored to public/vendor/sigta/fonts
# → sigta-ui.md + sigta-components.md written to project root
# → AGENTS.md gets a one-line pointer (created if absent)

# Optionally, set-and-forget — add to composer.json:
#   "post-autoload-dump": ["@php artisan sigta:install --ansi"]
# Now every composer install/update re-syncs the LLM context automatically.
```

An LLM then dropped into that consumer project reads `AGENTS.md` → finds the pointer to `sigta-ui.md` → reads the rules → reads `sigta-components.md` for the API → writes `<sigta:button>` instead of `<button class="bg-blue-600">`.

---

## Verification after implementation
- `cd playground && php artisan view:clear && php artisan sigta:install` — runs clean, prints summary
- Inspect: `playground/sigta-ui.md`, `playground/sigta-components.md`, `playground/AGENTS.md` (if created) all written
- Re-run `php artisan sigta:install` — idempotent, no errors, fonts truly mirrored
- `./vendor/bin/pest` — package tests still green
- `./scripts/check-tokens.sh` — clean (no hex introduced)
- Visit `/kit/installation` — new "09 · LLM agent context" section renders

## Not doing
- Generating the reference doc from code at build time (too brittle for now — hand-authored stub, with a CONVENTIONS.md note to update on component changes). A generator script is a clean future enhancement.
- A standalone lint script for consumer CI (mentioned earlier as a future layer; not part of this batch)