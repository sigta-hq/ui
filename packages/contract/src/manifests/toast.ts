import { defineComponent } from '../schema.ts';
import { c } from '../examples.ts';

const show = (detail: Record<string, unknown>) => ({ script: `window.Sigta.toast(${JSON.stringify(detail)})`, wait: 500 });

export default defineComponent({
    name: 'toast',
    title: 'Toast',
    tier: 'components',
    page: 'toast',
    svelte: 'Toast',
    description: 'The toast region: a fixed stack of paper notes with an inked edge in the variant colour, an optional link or action ("Undo"), dismissed by hand or after a while. Include it once per layout; raise toasts from anywhere.',
    aria: 'Each toast is role="status" (role="alert" when danger); the dismiss button is labelled.',
    props: {
        position: { type: 'enum', values: ['bottom-end', 'bottom-start', 'top-end', 'top-start'], default: 'bottom-end', description: 'Corner of the viewport; bottom ones rise above a visible floating bar.' },
    },
    parts: {
        item: { description: 'One toast (data-variant: success, danger, warning, info).' },
        icon: { description: 'The variant icon.' },
        body: { description: 'Heading, text, link and action.' },
        heading: { description: 'Optional bold first line.' },
        link: { description: 'Optional link.' },
        action: { description: 'Optional action button.' },
        dismiss: { description: 'The close button.' },
    },
    states: {
        variant: { attribute: 'data-variant', values: ['success', 'danger', 'warning', 'info'], description: 'On an item.' },
        leaving: { attribute: 'data-leaving', description: 'On an item that was dismissed and is sliding off.' },
    },
    events: {
        toast: {
            description: 'Raise a toast: { text, heading?, variant?, duration? (ms, 0 = stay), link? { text, href, target, rel, download, navigate }, action? { text, event, detail } | { text, onClick }, key? (replaces the toast with the same key) }.',
            blade: 'sigta-toast (window) — Sigta::toast() from PHP, $sigta.toast() in Alpine, window.Sigta.toast()',
            svelte: 'sigta.toast() (import { sigta } from \'@sigta-hq/svelte\'), window.Sigta.toast(), or the Inertia page prop sigta.toasts',
            detail: 'ToastDetail',
        },
    },
    uses: ['icon'],
    usage: {
        livewire: `@persist('toast')
    <sigta:toast />
@endpersist

{{-- PHP --}}
Sigta::toast('Your changes have been saved.');
Sigta::toast(heading: 'Changes saved.', text: 'You can always update this in your settings.', variant: 'success', duration: 5000, link: ['text' => 'View settings', 'href' => '/settings', 'navigate' => true]);`,
        svelte: `<!-- root layout -->
<Toast />

<script>
    import { sigta } from '@sigta-hq/svelte';
    sigta.toast('Your changes have been saved.');
    sigta.toast({ heading: 'Changes saved.', text: 'You can always update this in your settings.', variant: 'success', duration: 5000, link: { text: 'View settings', href: '/settings' } });
</script>`,
    },
    notes: {
        shared: [
            'Variants: success, warning, danger, info.',
            'An action (e.g. Undo after a completion) adds a button that fires its event — or runs onClick from JS — and closes the toast. A key replaces the showing toast with the same key instead of stacking another.','Mount it once per layout, near the end of <body>.', 'Use a key to replace rather than stack (ticking off five items leaves one toast).'],
        livewire: [
            'From Alpine: $sigta.toast(\'Saved\') or $sigta.toast({ heading: \'…\', variant: \'warning\' }); from JS: window.Sigta.toast(…); low level: $this->dispatch(\'sigta-toast\', heading: \'Saved\', variant: \'success\').',
            'action: [\'text\' => \'Undo\', \'event\' => \'task-reopen\', \'detail\' => [\'id\' => $id]] fires through Livewire, so an #[On(\'task-reopen\')] method receives $id.',
            'From PHP: Sigta::toast(\'Saved\') or Sigta::toast(heading: \'…\', variant: \'success\', action: [\'text\' => \'Undo\', \'event\' => \'task-reopen\', \'detail\' => [\'id\' => 5]]). An action\'s event goes through Livewire, so #[On] listeners hear it.',
            'With wire:navigate, wrap it in @persist(\'toast\') so toasts survive page transitions.',
        ],
        svelte: [
            'import { sigta } from \'@sigta-hq/svelte\'; sigta.toast(\'Saved\', { variant: \'success\' }).',
            'From any backend through Inertia: return { sigta: { toasts: [{ text, variant }] } } in the page props and use the inertia subpath.',
        ],
    },
    examples: [
        {
            label: 'Variants',
            conformance: 'browser',
            tree: [c('toast', {})],
            interactions: [
                show({ heading: 'Deploy complete.', text: 'Plumb 2.4 is live for all workspaces.', variant: 'success', duration: 0 }),
                show({ heading: 'Heads up.', text: 'Two seats are unassigned.', variant: 'warning', duration: 0 }),
                show({ text: 'Deploy failed — rolled back.', variant: 'danger', duration: 0 }),
                show({ text: 'Invoice created.', variant: 'info', link: { text: 'View invoice', href: '#invoice' }, action: { text: 'Undo', event: 'invoice-undo' }, duration: 0 }),
            ],
        },
        {
            label: 'Top start',
            conformance: 'browser',
            tree: [c('toast', { position: 'top-start' })],
            interactions: [show({ text: 'Saved.', variant: 'success', duration: 0 })],
        },
    ],
});
