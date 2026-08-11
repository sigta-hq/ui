# Kit page authoring — showing live component + source

Every kit page demo must show the rendered component AND its copyable source,
from a single authored snippet. Use `<x-kit-example>`.

## The rule

Inside `<x-kit-example>` write the usage snippet **with `«` in place of every `<`**.
Everything else (`>`, `"`, `/`, attributes) stays literal. The component
unescapes `«` → `<`, renders it live, and prints the literal as source with a
copy button.

Why `«`: the `sigta:` prefix is a global precompiler that rewrites real `<sigta:`
everywhere in the raw file (even inside strings), so a literal `<sigta:button>`
in a page can't be shown as text. `«` dodges it.

## Pattern

```blade
<x-kit-shell title="Button">
    <x-kit-example label="Variants">
    «sigta:button variant="primary">Save draft«/sigta:button>
    «sigta:button variant="ghost">Cancel«/sigta:button>
    </x-kit-example>

    <x-kit-example label="On a dark band">
    «div class="rounded-frame bg-inverse p-8 flex gap-4">
        «sigta:button variant="inverse">Book«/sigta:button>
    «/div>
    </x-kit-example>
</x-kit-shell>
```

## Rules

- Replace **every** `<` with `«` — opening tags, closing tags, self-closing
  (`«sigta:icon name="x" />`), and any wrapper HTML (`«div>…«/div>`).
- The snippet is the teaching material: write **correct, copy-pasteable** usage
  in the preferred `sigta:` prefix form.
- Group related variants/states into one `<x-kit-example label="…">` (mirror the
  coverage the page already had — don't drop variants).
- Components that need a parent to make sense must include it in the snippet so
  it renders AND teaches: `«sigta:tabs>…«sigta:tabs.tab>` , dropdown items inside
  `«sigta:dropdown>`, table cells inside `«sigta:table>…«sigta:table.row>`, etc.
- Interactive demos (modal, toast, command, context-menu, tooltip) keep their
  `x-on:`/trigger markup in the snippet — they render live and hydrate.
- Keep any layout wrappers as `«div class="max-w-md">…«/div>` where the old demo
  had them.
- Tokens/utilities only — no raw hex.

## Verify each page

- `http://sigta-ui.test/kit/<name>` returns **200** (a broken snippet 500s the
  whole page — nested render).
- `cd playground && php artisan test` stays green.
