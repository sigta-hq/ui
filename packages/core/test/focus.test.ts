// @vitest-environment jsdom
// The drawer's overlay behavior, as Alpine's x-trap.noscroll does it for the
// Livewire runtime: focus moves in, wraps around, cannot leave, and goes back
// on release; the page stops scrolling until the last lock lets go.
import { afterEach, describe, expect, it } from 'vitest';
import { lockScroll, tabbables, trapFocus } from '../src/focus.ts';

const html = (markup: string): HTMLElement => {
    document.body.innerHTML = markup;
    return document.body;
};
const tab = (shiftKey = false) => {
    const event = new KeyboardEvent('keydown', { key: 'Tab', shiftKey, bubbles: true, cancelable: true });
    (document.activeElement ?? document.body).dispatchEvent(event);
    return event;
};

afterEach(() => {
    document.body.innerHTML = '';
    document.documentElement.removeAttribute('style');
});

describe('tabbables', () => {
    it('lists what Tab reaches, in tab order', () => {
        html(`<div id="c">
            <a>no href</a>
            <a href="#" id="a">link</a>
            <button id="b">button</button>
            <button disabled>off</button>
            <input type="hidden">
            <span tabindex="-1">programmatic</span>
            <div tabindex="2" id="d">second</div>
            <div tabindex="1" id="e">first</div>
        </div>`);
        expect(tabbables(document.getElementById('c')!).map((el) => el.id)).toEqual(['e', 'd', 'a', 'b']);
    });

    it('skips what is not rendered or inert', () => {
        html(`<div id="c">
            <button id="a" style="display:none">gone</button>
            <div style="display:none"><button>inside gone</button></div>
            <button hidden>hidden</button>
            <div inert><button>inert</button></div>
            <button style="visibility:hidden">invisible</button>
            <button id="b">here</button>
        </div>`);
        expect(tabbables(document.getElementById('c')!).map((el) => el.id)).toEqual(['b']);
    });
});

describe('trapFocus', () => {
    const setup = () => {
        html(`<button id="opener">open</button>
            <nav id="drawer"><button id="close">close</button><a href="#" id="one">one</a><a href="#" id="two">two</a></nav>
            <button id="outside">outside</button>`);
        document.getElementById('opener')!.focus();
        return document.getElementById('drawer')!;
    };

    it('moves focus to the first tabbable element inside', () => {
        const release = trapFocus(setup());
        expect(document.activeElement?.id).toBe('close');
        release();
    });

    it('prefers an [autofocus] element, and leaves focus that is already inside', () => {
        const drawer = setup();
        document.getElementById('two')!.setAttribute('autofocus', '');
        let release = trapFocus(drawer);
        expect(document.activeElement?.id).toBe('two');
        release();

        document.getElementById('one')!.focus();
        release = trapFocus(drawer, { returnFocus: false });
        expect(document.activeElement?.id).toBe('one');
        release();
    });

    it('wraps Tab and Shift+Tab around the ends', () => {
        const release = trapFocus(setup());
        document.getElementById('two')!.focus();
        expect(tab().defaultPrevented).toBe(true);
        expect(document.activeElement?.id).toBe('close');
        tab(true);
        expect(document.activeElement?.id).toBe('two');
        // In the middle the browser moves focus itself.
        document.getElementById('one')!.focus();
        expect(tab().defaultPrevented).toBe(false);
        release();
    });

    it('brings focus back inside when it lands outside', () => {
        const release = trapFocus(setup());
        document.getElementById('one')!.focus();
        document.getElementById('outside')!.focus();
        expect(document.activeElement?.id).toBe('one');
        release();
    });

    it('gives focus back to what had it, and stops trapping, on release', () => {
        const release = trapFocus(setup());
        release();
        expect(document.activeElement?.id).toBe('opener');
        document.getElementById('outside')!.focus();
        expect(document.activeElement?.id).toBe('outside');
        expect(tab().defaultPrevented).toBe(false);
    });

    it('focuses a drawer that is still being revealed once it shows', async () => {
        const drawer = setup();
        drawer.style.display = 'none';
        const release = trapFocus(drawer);
        expect(document.activeElement?.id).toBe('opener');
        drawer.style.removeProperty('display');
        await new Promise((resolve) => requestAnimationFrame(() => resolve(null)));
        expect(document.activeElement?.id).toBe('close');
        release();
    });
});

describe('lockScroll', () => {
    it('hides the page overflow and pads by the scrollbar, then restores it', () => {
        document.documentElement.style.overflow = 'scroll';
        const release = lockScroll();
        expect(document.documentElement.style.overflow).toBe('hidden');
        expect(document.documentElement.style.paddingRight).toMatch(/^\d+px$/);
        release();
        expect(document.documentElement.style.overflow).toBe('scroll');
        expect(document.documentElement.style.paddingRight).toBe('');
    });

    it('holds until every overlapping lock is released', () => {
        const first = lockScroll();
        const second = lockScroll();
        first();
        first();
        expect(document.documentElement.style.overflow).toBe('hidden');
        second();
        expect(document.documentElement.style.overflow).toBe('');
    });
});
