import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

export default defineComponent({
    name: 'button',
    title: 'Button',
    tier: 'elements',
    page: 'button',
    svelte: 'Button',
    description: 'The action control: ink fill, outline, ghost and danger variants, a dark-band pair, and a busy state that writes the compass in once an action outlasts a beat. Renders a link when given an href.',
    aria: 'Native <button>; <a> when href is set.',
    props: {
        variant: {
            type: 'enum',
            values: ['primary', 'secondary', 'ghost', 'cta', 'danger', 'inverse', 'ghost-inverse'],
            default: 'secondary',
            description: 'primary/cta = ink fill; secondary = ink outline; ghost = borderless; danger = red fill; inverse / ghost-inverse = paper fill / hairline outline for dark bands.',
        },
        size: { type: 'enum', values: ['sm', 'base'], default: 'base', description: 'Control size.' },
        loading: {
            type: 'boolean',
            default: null,
            description: 'Busy state. Livewire infers it from wire:click / wire:submit / type="submit" and shows the compass once the action outlasts a beat; true shows it now, false never. Svelte: pass the in-flight flag (loading={form.processing}); the compass appears once it outlasts a beat.',
        },
        href: { type: 'string', default: null, native: true, description: 'Renders an <a> with the button look. target="_blank" gets rel="noopener" unless rel is given.' },
        type: { type: 'enum', values: ['button', 'submit', 'reset'], default: 'button', native: true, description: 'The native button type. A submit button carries the busy markup automatically.' },
    },
    parts: {
        busy: { description: 'Holds the compass loader shown while busy (only rendered when the button can be busy).' },
        label: { description: 'Wraps the slot when the button can be busy, so the compass can stand in for a leading icon.' },
    },
    states: {
        loading: { attribute: 'data-loading', description: 'An action is in flight (set by Livewire, or from the loading prop).' },
        busy: { attribute: 'data-sigta-busy', description: 'The action outlasted a beat; the compass shows.' },
        leadIcon: { attribute: 'data-lead-icon', conformance: false, description: 'The label starts with an icon, which the compass replaces while busy. Svelte sets it on the client.' },
        disabled: { attribute: 'disabled', description: 'Dimmed and inert — except a submitting form\'s buttons, which keep their ink.' },
    },
    slots: {
        default: { description: 'The label; icons go inline (leading or trailing).', required: true },
    },
    usage: {
        livewire: `<sigta:button variant="cta" href="/book">Book a project <sigta:icon name="arrow-right" size="sm" /></sigta:button>
<sigta:button href="https://calendly.com/sf-sigta/30min" target="_blank">Book a call</sigta:button>
<sigta:button>Preview</sigta:button>
<sigta:button variant="ghost">Cancel</sigta:button>
<sigta:button variant="primary" wire:click="save">Save</sigta:button>   {{-- compass while it saves --}}`,
        svelte: `<Button variant="cta" href="/book">Book a project <Icon name="arrow-right" size="sm" /></Button>
<Button href="https://calendly.com/sf-sigta/30min" target="_blank">Book a call</Button>
<Button>Preview</Button>
<Button variant="ghost">Cancel</Button>
<Button variant="primary" loading={form.processing} onclick={save}>Save</Button>   <!-- compass while it saves -->`,
    },
    notes: {
        shared: [
            'Renders <a> when href is set, else <button>. Lifts on hover, settles back onto the paper while pressed, springs back on release.',
            'Use variant="cta" with a trailing arrow-right icon for the main call to action.',
            'On a dark band (bg-inverse) use variant="inverse" and "ghost-inverse" — never override colours.',
            'A link button with target="_blank" gets rel="noopener" unless rel is set.',
            'The label never gives way to the loader: a quick action shows nothing; once one outlasts a beat (--sigta-dur-busy, 500ms) the compass stands in for a leading icon, or is written in before a label that has none. A submitting form\'s button keeps its ink while it is disabled for the submit. Never write "Saving…" into a button label yourself.',
        ],
        livewire: [
            'wire:click / wire:submit / type="submit" add the busy markup and the button takes no clicks while Livewire runs its action (Livewire sets data-loading, on the form for a submit); the runtime shows the compass after a beat.',
            ':loading="true" shows the busy state now (a button you drive yourself); :loading="false" never shows it.',
        ],
        svelte: [
            'Pass loading={form.processing} (or any in-flight flag). The busy markup is rendered whenever loading is given or type="submit".',
            'Omit loading for a button that is never busy.',
        ],
    },
    examples: [
        {
            label: 'Variants',
            tree: [
                c('button', { variant: 'primary' }, 'Save draft'),
                c('button', {}, 'Preview'),
                c('button', { variant: 'ghost' }, 'Cancel'),
                c('button', { variant: 'cta' }, 'Book a project ', c('icon', { name: 'arrow-right', size: 'sm' })),
                c('button', { variant: 'danger' }, 'Delete'),
            ],
        },
        {
            label: 'States & sizes',
            tree: [
                c('button', { variant: 'primary', disabled: true }, 'Disabled'),
                c('button', { href: '#top', variant: 'secondary' }, 'As link'),
                c('button', { href: 'https://example.com', target: '_blank', variant: 'cta' }, 'Opens in a new tab'),
                c('button', { variant: 'primary', size: 'sm' }, 'Small'),
            ],
        },
        {
            label: 'Busy',
            tree: [
                c('button', { variant: 'primary', loading: true }, 'Exporting'),
                c('button', { type: 'submit', variant: 'cta' }, c('icon', { name: 'check', size: 'sm' }), ' Send'),
            ],
        },
        {
            label: 'Inverse variants (for dark / ink bands)',
            frame: 'inverse',
            tree: [
                el('div', { class: 'rounded-frame bg-inverse p-8 flex flex-wrap items-end gap-4' },
                    c('button', { variant: 'inverse' }, 'Book en intro-samtale'),
                    c('button', { variant: 'ghost-inverse' }, 'Skriv en mail'),
                ),
            ],
        },
    ],
});
