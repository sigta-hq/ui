# Contributing to Sigta UI

Thanks for helping. Bug reports, fixes, new components and docs are all
welcome. Please follow the [code of conduct](CODE_OF_CONDUCT.md) in every
issue, discussion and pull request.

- **Questions** ("how do I…?") go to
  [Discussions](https://github.com/sigta-hq/ui/discussions), not issues.
- **Bugs and feature requests** go to
  [issues](https://github.com/sigta-hq/ui/issues/new/choose). Search first,
  and for anything larger than a fix, open an issue before writing code, so we
  can agree on the API before you spend time on it.
- **Security problems** must not go in public issues. See
  [SECURITY.md](SECURITY.md).

## Setting up

You need PHP 8.4, Composer and Node 22.

```bash
git clone https://github.com/sigta-hq/ui.git && cd ui
composer install
./vendor/bin/pest            # package tests
./scripts/check-tokens.sh    # token guard: no raw hex outside the theme files
```

The `playground/` folder is a Laravel app that uses the package through a path
repository. It is the docs site, the kit pages and the visual check for every
component:

```bash
cd playground
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
npm run dev & php artisan serve      # kit pages at http://localhost:8000/kit
php artisan test
```

## How the repo is laid out

| Path | What it is |
|---|---|
| `src/` | Service provider, asset routes, `sigta:install` command |
| `config/sigta.php` | Package config (published with `--tag=sigta-config`) |
| `resources/views/components/` | The Blade components (`<sigta:button>` = `button.blade.php`) |
| `resources/css/` | `core.css` (structure), `theme.css` (Sigta theme), tokens |
| `resources/js/` | Alpine runtime (`sigta.js`) and the TipTap editor source |
| `dist/` | Built JS that ships to apps. Committed, never edited by hand |
| `stubs/` | The LLM agent docs that `sigta:install` copies into apps |
| `tests/` | Pest tests for the package |
| `playground/` | Docs site and kit pages (not part of the Composer download) |

## The rules that CI enforces

Read [CONVENTIONS.md](CONVENTIONS.md) before touching a component, and
[SECTION-CONVENTIONS.md](SECTION-CONVENTIONS.md) before touching a section.
The short version:

- **Tokens only.** Components use `var(--sigta-*)` tokens and token
  utilities, never raw colours. `scripts/check-tokens.sh` fails otherwise.
- **No Sigta content in the package.** Components ship no copy, links, logo or
  client names; sites pass their own. `tests/NoBrandContentTest.php` fails
  otherwise. Example content belongs in the playground.
- **`dist/` matches its sources.** If you change `resources/js/`, run
  `npm ci && npm run build` and commit the result, including
  `dist/sigta-editor.js.LICENSE.txt`. CI rebuilds and fails on any difference.
- **Every kit page renders.** A broken snippet turns its whole page into a 500,
  and the playground tests load every page. See
  [playground/KIT-AUTHORING.md](playground/KIT-AUTHORING.md).

## A change is done when

1. The component (and its Alpine behaviour, if interactive) works.
2. Pest tests cover its content, each variant and state, and attribute
   forwarding.
3. Its kit page in `playground/resources/views/kit/` shows every variant and
   state.
4. `stubs/sigta-components.md` describes it (and `stubs/sigta-ui.md`, if a rule
   changed). Apps' coding agents read only these files, so a component missing
   from them doesn't exist for them.
5. These all pass:

   ```bash
   ./vendor/bin/pest
   ./scripts/check-tokens.sh
   cd playground && php artisan view:clear && php artisan test
   ```

## Pull requests

- Branch from `main` and keep each pull request to one change.
- Write commit messages as a plain sentence about what changes for the user
  ("Let Enter move one line down in the editor"), with the why in the body.
- Fill in the pull request template. Say if the change breaks an existing API:
  breaking changes are fine before 1.0, but they need a note for people
  upgrading.
- Screenshots or a short clip help a lot for anything visual.

By submitting a pull request you agree that your contribution is licensed under
the [MIT license](LICENSE), like the rest of the project.

## Releases (maintainers)

Versions follow [semantic versioning](https://semver.org). Before 1.0, a minor
release (0.2.0) may contain breaking changes; a patch release (0.1.1) never
does.

1. Merge the pull requests for the release into `main`. Label each one
   (`breaking change`, `enhancement`, `bug`, `documentation`) so the release
   notes group it.
2. Tag `main` and push the tag:

   ```bash
   git switch main && git pull
   git tag -a v0.2.0 -m "v0.2.0"
   git push origin v0.2.0
   ```

3. The release workflow publishes a GitHub Release with notes generated from
   the merged pull requests, and Packagist picks up the tag within a minute.
   Edit the release afterwards to add upgrade notes for breaking changes.
