# Security policy

## Supported versions

Sigta UI is before 1.0, so only the latest release gets security fixes. Fixes
ship as a new release; update with `composer update sigta/ui`.

## Reporting a vulnerability

Please do not report security problems in public issues, discussions or pull
requests.

Report them privately through GitHub:
[open a security advisory](https://github.com/sigta-hq/ui/security/advisories/new).
If you can't use GitHub, email [sf@sigta.dk](mailto:sf@sigta.dk).

Include what you can of:

- the affected component, file or route
- the versions of `sigta/ui`, Laravel, Livewire and PHP
- steps to reproduce, or a proof of concept
- what an attacker could do with it

We aim to acknowledge a report within five working days and to keep you
updated until it is fixed. Once a fix is released, we publish an advisory and
credit you, unless you would rather stay anonymous.

## What counts

Anything in the package that a site using it could be exposed through, for
example cross-site scripting through a component that renders user input
unescaped, or the asset routes serving files they shouldn't. The playground
is a development app and not deployed, so issues only reachable there are
ordinary bugs.
