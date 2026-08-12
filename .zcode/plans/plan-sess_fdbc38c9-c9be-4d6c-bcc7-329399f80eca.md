## Summary

Upgrade the package from Livewire 3.5 → 4.x (latest is v4.4.0). The blast radius is **small** because this package is an anonymous-Blade + Alpine library, not a Livewire-component library — it has zero `extends Component`, zero `Livewire::` facade calls, zero `Wireable`, zero lifecycle hooks. Its coupling to Livewire is narrow and architectural: Livewire ships Alpine, and the runtime JS hooks `alpine:init`.

## What the research confirmed (why this is low-risk)

From the [Livewire 4 upgrade guide](https://livewire.laravel.com/docs/4.x/upgrading) and [installation docs](https://livewire.laravel.com/docs/4.x/installation):

- **`@livewireStyles` and `@livewireScripts` still exist in v4** — not deprecated, not renamed. The 5 playground layouts using them need **no changes**.
- **Alpine still ships with Livewire** and `alpine:init` still fires. The `resources/js/sigta.js` and `editor.js` boot pattern (`document.addEventListener('alpine:init', …)`) **works unchanged**.
- **`Livewire\LivewireServiceProvider` still exists** at that FQN — `tests/TestCase.php` registration works unchanged.
- **PHP `^8.1`, Laravel `^10|^11|^12|^13`** — the package already requires PHP `^8.2` and illuminate `^12|^13`, so v4.4.0 satisfies cleanly.
- **Asset serving** — the package's route-based `AssetController` is fully decoupled from Livewire's asset pipeline. Unaffected.
- **`wire:model` modifier change** — v4 changed `.blur`/`.change` to require `.live`. But this package only **forwards** `wire:model` through the attribute bag; it never authors `wire:model` directives. The package is unaffected; host apps adapt. Worth a doc note only.

The two genuine unknowns to verify empirically (can't be confirmed from docs alone):
1. Does `LivewireServiceProvider` boot cleanly under Orchestra Testbench in v4? (Highly likely — same class name, same boot contract.)
2. Does `playground/config/livewire.php`'s `navigate.progress_bar_color` key still apply in v4? (Likely — navigate config shape isn't called out as a breaking change, but the two playground feature tests assert zero raw hex, so they'll catch a regression.)

## The plan

### 1. Bump the constraint — `composer.json` (package)
Change `"livewire/livewire": "^3.5"` → `"^4.0"`. This is the only version string that drives both the package and the playground (playground has no separate pin; it inherits via the `sigta/ui: @dev` path repo).

### 2. Resolve & install
- `composer update livewire/livewire --with-all-dependencies` in the **package root** (updates `composer.lock`)
- `composer update livewire/livewire --with-all-dependencies` in **playground/** (gets the propagated v4 via the symlink)

If dependency resolution fails (e.g. another package holds it to v3), surface that — don't force.

### 3. Verify nothing broke — the real test
- **Package tests:** `./vendor/bin/pest` — 243 tests, all the ones asserting `wire:model` forwarding, Alpine `x-data` presence, etc. Must stay green. The `TestCase.php` Livewire provider registration is the one thing that could fail if v4 renamed the class; expect it to pass.
- **Token guard:** `./scripts/check-tokens.sh` — if `navigate.progress_bar_color` regressed in v4, raw `#2299dd` hex would re-appear in rendered pages. This catches that.
- **Playground smoke:** `php artisan view:clear && php artisan test` — the `DocsPageTest` and `DashboardPageTest` assert zero raw hex, which transitively checks the navigate override still works under v4.

### 4. Update version references in docs
The `/kit/installation` page and README reference version requirements. Update:
- `playground/resources/views/kit/installation.blade.php` — the requirements block currently lists `Livewire ^3.5`; bump to `^4.0`
- `README.md` — only references Tailwind, not Livewire version (verified — no change needed there)
- `stubs/sigta-components.md` / `stubs/sigta-ui.md` — check for any Livewire version mention (likely none; verified during exploration)
- `CONVENTIONS.md` — "Livewire integration" section references patterns, not versions (verified — no change needed)

### 5. Conditionally fix the navigate config (only if token guard / playground tests fail)
If step 3 reveals that v4 renamed/dropped `progress_bar_color`, update `playground/config/livewire.php` to the new key (or document that the override is no longer needed if v4 removed the raw hex). I'll only touch this if the tests demand it — not speculatively.

### 6. Add a one-line doc note on the wire:model.live change
In the `stubs/sigta-components.md` form-components section or `CONVENTIONS.md`'s Livewire integration section, add a brief note: under Livewire 4, host apps adding `.blur`/`.change` to `wire:model` on Sigta inputs must use `wire:model.live.blur` to get v3 behavior. This helps consumer apps without changing package code.

## Not doing
- No changes to `resources/js/sigta.js` or `editor.js` — the Alpine boot pattern is unchanged in v4.
- No changes to `@livewireStyles`/`@livewireScripts` in the 5 layouts — the directives still exist.
- No changes to `tests/TestCase.php` — the provider FQN is the same in v4.
- No `wire:model` directive changes — the package forwards attributes; it doesn't author directives.

## Rollback
If anything in step 3 fails unexpectedly, revert the constraint to `^3.5` and report what broke. The change is one line + lockfiles; rollback is trivial.

## Verification gate (must all pass before done)
- `./vendor/bin/pest` → 243 passed
- `./scripts/check-tokens.sh` → clean
- `cd playground && php artisan view:clear && php artisan test` → green
- `composer show livewire/livewire` shows `4.x` in both package and playground