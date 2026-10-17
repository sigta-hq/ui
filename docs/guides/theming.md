---
title: Theming
order: 2
---

# Theming

Every component reads semantic `--sigta-*` tokens and never a raw colour.
Swap the values and the same markup carries a different brand — at build
time, or at runtime by setting `data-sigta-theme` on any subtree. The tokens
are the same for both runtimes.

## Drop-in Sigta brand

<!-- @livewire -->
```css
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/sigta.css";
```
<!-- @end -->
<!-- @svelte -->
```css
@import "tailwindcss";
@import "@sigta-hq/contract/tailwind.css";
@import "@sigta-hq/svelte/sigta.css";
```
<!-- @end -->

## Your own brand

Import the structure without the Sigta values, then your theme:

<!-- @livewire -->
```css
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/core.css";   /* structure only */
@import "./brand.css";                                     /* your :root{--sigta-*} + @font-face */
```
<!-- @end -->
<!-- @svelte -->
```css
@import "tailwindcss";
@import "@sigta-hq/contract/tailwind.css";
@import "@sigta-hq/contract/core.css";   /* structure only */
@import "./brand.css";                   /* your :root{--sigta-*} + @font-face */
```
<!-- @end -->

Start from the template, which documents every token:
<!-- @livewire -->
`vendor/sigta/ui/packages/contract/css/theme.example.css`.
<!-- @end -->
<!-- @svelte -->
`node_modules/@sigta-hq/contract/css/theme.example.css`.
<!-- @end -->

```css
:root {
    --sigta-surface: …;        /* page background */
    --sigta-surface-raised: …; /* cards, inputs, panels */
    --sigta-fg: …;             /* primary text, borders, solid fills */
    --sigta-fg-muted: …;       /* secondary text */
    --sigta-line: …;           /* hairlines, dividers, input borders */
    --sigta-accent: …;         /* the one emphasis colour (used sparingly) */
    --sigta-font-head: …;  --sigta-font-body: …;  --sigta-font-mono: …;  --sigta-font-hand: …;
    --sigta-radius: …;  --sigta-radius-lg: …;  --sigta-radius-xl: …;  --sigta-shadow: …;
    /* … see the template for the full list */
}
```

## Several brands in one app

Scope a theme and flip it on any subtree:

```css
[data-sigta-theme="studio"] { --sigta-accent: …; --sigta-radius: …; }
```

```html
<section data-sigta-theme="studio">…</section>
```

## Overriding one component

A component's look lives in the stylesheet's `components` layer, below
Tailwind's `utilities`, so a class you pass always wins:

<!-- @livewire -->
```blade
<sigta:button class="w-full">Continue</sigta:button>
```
<!-- @end -->
<!-- @svelte -->
```svelte
<Button class="w-full">Continue</Button>
```
<!-- @end -->

Prefer the component's variants over overrides — they are the brand.
