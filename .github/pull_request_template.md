## What changes

<!-- What this does for someone using the library, and why. Link the issue it closes: "Closes #123". -->

## Breaking?

<!-- Does an existing prop, slot, token, event or default change? If so, what should people upgrading do? Otherwise write "No". -->

## Checklist

- [ ] Pest tests cover the change (`./vendor/bin/pest`)
- [ ] `./scripts/check-tokens.sh` is clean
- [ ] The kit page shows every variant and state, and `cd playground && php artisan test` passes
- [ ] `stubs/sigta-components.md` (and `stubs/sigta-ui.md`, if a rule changed) describes the change
- [ ] `dist/` rebuilt with `npm run build`, if `resources/js/` changed
- [ ] No Sigta copy, links or logo in `resources/`, `src/` or `stubs/`

## Screenshots

<!-- For anything visual. -->
