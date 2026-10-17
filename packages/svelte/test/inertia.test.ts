// The Inertia bridge: a `sigta` page prop becomes toasts and celebrations,
// and non-GET visits drive a named save state.
import { beforeEach, describe, expect, it, vi } from 'vitest';

const handlers = new Map<string, Array<(event: unknown) => void>>();
vi.mock('@inertiajs/svelte', () => ({
    router: {
        on: (type: string, callback: (event: unknown) => void) => {
            handlers.set(type, [...(handlers.get(type) ?? []), callback]);
            return () => handlers.set(type, (handlers.get(type) ?? []).filter((c) => c !== callback));
        },
    },
}));

const { connectInertia, applySigtaProps } = await import('../src/lib/inertia/index.ts');
const fire = (type: string, detail: unknown) => (handlers.get(type) ?? []).forEach((h) => h(new CustomEvent(type, { detail })));

const events: Array<[string, unknown]> = [];
for (const name of ['sigta-toast', 'sigta-save-state']) {
    window.addEventListener(name, (e) => events.push([name, (e as CustomEvent).detail]));
}

describe('inertia bridge', () => {
    beforeEach(() => {
        handlers.clear();
        events.length = 0;
    });

    it('turns the sigta page prop into toasts, once per page', () => {
        const stop = connectInertia();
        const page = { props: { sigta: { toasts: [{ text: 'Saved', variant: 'success' }] } } };
        fire('navigate', { page });
        fire('success', { page });
        expect(events.filter(([n]) => n === 'sigta-toast')).toEqual([['sigta-toast', { text: 'Saved', variant: 'success' }]]);
        stop();
        expect(handlers.get('navigate')).toEqual([]);
    });

    it('reads another prop name when asked', () => {
        connectInertia({ prop: 'flash' });
        fire('navigate', { page: { props: { flash: { toasts: [{ text: 'Hi' }] } } } });
        expect(events.some(([n, d]) => n === 'sigta-toast' && (d as { text: string }).text === 'Hi')).toBe(true);
    });

    it('drives a named save state from writing visits only', () => {
        connectInertia({ saveState: 'profile' });
        fire('start', { visit: { method: 'get' } });
        fire('success', { page: { props: {} } });
        expect(events.filter(([n]) => n === 'sigta-save-state')).toEqual([]);
        fire('start', { visit: { method: 'put' } });
        fire('success', { page: { props: {} } });
        fire('start', { visit: { method: 'post' } });
        fire('error', { errors: { name: 'Required' } });
        expect(events.filter(([n]) => n === 'sigta-save-state').map(([, d]) => (d as { state: string }).state)).toEqual(['saving', 'saved', 'saving', 'failed']);
    });

    it('ignores pages without the prop', () => {
        applySigtaProps(undefined);
        applySigtaProps({});
        expect(events).toEqual([]);
    });
});
