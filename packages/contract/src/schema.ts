/* ---------------------------------------------------------------------------
   The component contract.

   One manifest per component describes everything both runtimes must agree
   on: props (with neutral types and defaults), the parts and states the
   shared stylesheet keys on, slots, events, the ARIA pattern, notes for each
   runtime's agents and the examples that double as conformance fixtures.
   Vocabulary is neutral; each runtime maps it to its own syntax.
--------------------------------------------------------------------------- */
import type { ExampleNode } from './examples.ts';

export type Tier = 'elements' | 'components' | 'layout' | 'sections';

export type PropType = 'string' | 'number' | 'boolean' | 'enum' | 'array' | 'object' | 'date' | 'any';

export interface PropSpec {
    type: PropType;
    description: string;
    /** Neutral default; `null` means unset. */
    default?: unknown;
    /** Allowed values for an enum. */
    values?: readonly string[];
    required?: boolean;
    /** A native HTML attribute the component reads (href, type, name, disabled): forwarded in Blade, a plain prop in Svelte. */
    native?: boolean;
    /** Two-way: Svelte exposes it through `bind:`; Livewire binds it with `wire:model` on the native element. */
    bindable?: boolean;
    /** Shape of an array/object prop, in words or TypeScript. */
    shape?: string;
    /** Blade-specific: a different attribute name, extra accepted PHP types, or false when the prop is Svelte-only. */
    blade?: false | { name?: string; accepts?: string; note?: string };
    /** Svelte-specific: a different prop name, or false when the prop is Livewire-only. */
    svelte?: false | { name?: string; type?: string; note?: string };
}

export interface PartSpec {
    description: string;
}

export interface StateSpec {
    /** The attribute the stylesheet reads (`data-state`, `aria-invalid`, `data-selected`, …). */
    attribute: string;
    values?: readonly string[];
    description: string;
    /** false when one runtime sets it on the client only (left out of the conformance comparison). */
    conformance?: boolean;
}

export interface SlotSpec {
    description: string;
    /** Parameters the Svelte snippet receives, as a TypeScript tuple, e.g. `[item: Tier]`. */
    params?: string;
    /** Required to render meaningfully. */
    required?: boolean;
}

export interface EventSpec {
    description: string;
    /** How Livewire/Blade hears it: a DOM/window event name, e.g. `sigta-kanban-move`. */
    blade?: string | false;
    /** The Svelte callback prop, e.g. `onmove`. */
    svelte?: string | false;
    /** The payload, in TypeScript. */
    detail?: string;
}

export interface Example {
    label: string;
    /** Neutral tree rendered into both syntaxes; also a conformance fixture. */
    tree?: ExampleNode[];
    /** Per-runtime snippets for examples a tree cannot express (bindings, scripts). */
    blade?: string;
    svelte?: string;
    /** Leave this tree out of the conformance comparison (e.g. a demo with random ids it cannot normalize). */
    conformance?: boolean;
    /** Wrap the demo on a dark band, a narrow column, … in the docs. */
    frame?: 'inverse' | 'narrow' | 'wide';
}

export interface ComponentManifest {
    /** Tag name without prefix: `button`, `dropdown.item`, `section.hero`. */
    name: string;
    title: string;
    tier: Tier;
    /** Kit/docs page that showcases it. */
    page: string;
    description: string;
    /** The Svelte component's export path, e.g. `Button`, `Dropdown.Item`. */
    svelte: string;
    /** WAI-ARIA APG pattern or native element the component follows. */
    aria?: string;
    props: Record<string, PropSpec>;
    parts?: Record<string, PartSpec>;
    states?: Record<string, StateSpec>;
    slots?: Record<string, SlotSpec>;
    events?: Record<string, EventSpec>;
    /** Component names this one is composed of (for docs cross-links). */
    uses?: readonly string[];
    /** Rules and gotchas for agents, shared or per runtime. */
    notes?: { shared?: readonly string[]; livewire?: readonly string[]; svelte?: readonly string[] };
    examples: Example[];
}

/** Identity helper that types a manifest and checks its internal references. */
export function defineComponent<const T extends ComponentManifest>(manifest: T): T {
    for (const [name, prop] of Object.entries(manifest.props)) {
        if (prop.type === 'enum' && !prop.values?.length) {
            throw new Error(`${manifest.name}: enum prop "${name}" needs values`);
        }
        if (prop.type === 'enum' && prop.default != null && !prop.values!.includes(prop.default as string)) {
            throw new Error(`${manifest.name}: default of "${name}" is not one of its values`);
        }
    }
    return manifest;
}

/** The prop name a runtime uses. */
export function propName(spec: PropSpec, name: string, runtime: 'blade' | 'svelte'): string | null {
    const override = spec[runtime];
    if (override === false) return null;
    return override?.name ?? name;
}
