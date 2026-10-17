// @vitest-environment jsdom
// The editor both runtimes run: the toolbar, ids and paste rules as plain
// functions, then a real TipTap editor — markdown in and out, the toolbar's
// commands, links, and pictures and titles fetched from the host.
import { afterEach, beforeAll, describe, expect, it, vi } from 'vitest';
import {
    bindingId,
    createEditor,
    csrfToken,
    editorFormat,
    editorRows,
    editorTools,
    followLink,
    imageFiles,
    isLinkShortcut,
    resolveLink,
    sameOriginUrl,
    type EditorController,
} from '../src/editor.ts';

beforeAll(() => {
    // jsdom does no layout; ProseMirror asks ranges for their boxes when it scrolls the caret into view.
    const empty = () => ({ x: 0, y: 0, top: 0, left: 0, right: 0, bottom: 0, width: 0, height: 0, toJSON() {} }) as DOMRect;
    Range.prototype.getBoundingClientRect = empty;
    Range.prototype.getClientRects = () => ({ length: 0, item: () => null, [Symbol.iterator]: [][Symbol.iterator] }) as unknown as DOMRectList;
    document.elementFromPoint = () => null;
});

describe('the toolbar and its options', () => {
    it('reads the format, defaulting to html', () => {
        expect(editorFormat('markdown')).toBe('markdown');
        expect(editorFormat('html')).toBe('html');
        expect(editorFormat('rtf')).toBe('html');
        expect(editorFormat(undefined)).toBe('html');
    });

    it('lists the tools in order, with the picture tool only for uploads', () => {
        const labels = (tools: ReturnType<typeof editorTools>) => tools.map((tool) => tool?.label ?? '|');
        expect(labels(editorTools(false))).toEqual(['Bold', 'Italic', 'Strikethrough', 'Link', '|', 'Heading', 'Bullet list', 'Numbered list', 'Quote', 'Code block']);
        expect(labels(editorTools(true)).at(-1)).toBe('Picture');
        expect(editorTools(false)[5]).toMatchObject({ command: 'heading', icon: 'heading-2', attrs: { level: 2 } });
    });

    it('sizes by rows like a textarea: six, or three inline', () => {
        expect(editorRows(null, false)).toBe(6);
        expect(editorRows(undefined, true)).toBe(3);
        expect(editorRows('8', true)).toBe(8);
        expect(editorRows(4.7, false)).toBe(4);
    });

    it('derives the id from the binding as Laravel\'s Str::slug does', () => {
        expect(bindingId('body')).toBe('sigta-editor-body');
        expect(bindingId('description')).toBe('sigta-editor-description');
        expect(bindingId('form.body')).toBe('sigta-editor-formbody');
        expect(bindingId('post_body')).toBe('sigta-editor-post-body');
        expect(bindingId('Release Notes')).toBe('sigta-editor-release-notes');
        expect(bindingId('mail@home')).toBe('sigta-editor-mail-at-home');
        expect(bindingId('Café')).toBe('sigta-editor-cafe');
    });

    it('takes Cmd+K or Ctrl+K, and nothing with another modifier', () => {
        const key = (init: KeyboardEventInit) => new KeyboardEvent('keydown', { key: 'k', ...init });
        expect(isLinkShortcut(key({ metaKey: true }))).toBe(true);
        expect(isLinkShortcut(key({ ctrlKey: true }))).toBe(true);
        expect(isLinkShortcut(key({ key: 'K', ctrlKey: true }))).toBe(true);
        expect(isLinkShortcut(key({}))).toBe(false);
        expect(isLinkShortcut(key({ ctrlKey: true, shiftKey: true }))).toBe(false);
        expect(isLinkShortcut(key({ ctrlKey: true, metaKey: true }))).toBe(false);
        expect(isLinkShortcut(key({ key: 'j', ctrlKey: true }))).toBe(false);
    });
});

describe('paste and drop rules', () => {
    const site = { href: 'https://app.test/notes/1', origin: 'https://app.test' };

    it('titles only a single address on the page\'s own origin', () => {
        expect(sameOriginUrl('https://app.test/kit/kanban', site)).toBe('https://app.test/kit/kanban');
        expect(sameOriginUrl('  /kit/kanban ', site)).toBe('https://app.test/kit/kanban');
        expect(sameOriginUrl('https://elsewhere.test/x', site)).toBeNull();
        expect(sameOriginUrl('https://app.test/a https://app.test/b', site)).toBeNull();
        expect(sameOriginUrl('mailto:me@app.test', site)).toBeNull();
        expect(sameOriginUrl('', site)).toBeNull();
        expect(sameOriginUrl(null, site)).toBeNull();
    });

    it('keeps the pictures among the files', () => {
        const files = [new File(['a'], 'a.png', { type: 'image/png' }), new File(['b'], 'b.txt', { type: 'text/plain' })];
        expect(imageFiles({ files }).map((file) => file.name)).toEqual(['a.png']);
        expect(imageFiles(null)).toEqual([]);
    });

    it('sends the editor\'s token, else the page\'s', () => {
        expect(csrfToken('abc')).toBe('abc');
        expect(csrfToken('')).toBe('');
        document.head.innerHTML = '<meta name="csrf-token" content="from-meta">';
        expect(csrfToken('')).toBe('from-meta');
        expect(csrfToken(null)).toBe('from-meta');
        document.head.innerHTML = '';
    });

    it('follows a link only on Cmd/Ctrl+click', () => {
        const open = vi.spyOn(window, 'open').mockImplementation(() => null);
        document.body.innerHTML = '<a href="https://sigta.dk"><span>docs</span></a>';
        const span = document.querySelector('span')!;
        const click = (init: MouseEventInit) => {
            const event = new MouseEvent('click', { bubbles: true, cancelable: true, ...init });
            Object.defineProperty(event, 'target', { value: span });
            return event;
        };
        expect(followLink(click({}))).toBe(false);
        expect(followLink(click({ metaKey: true }))).toBe(true);
        expect(open).toHaveBeenCalledWith('https://sigta.dk', '_blank', 'noopener');
        open.mockRestore();
    });
});

describe('the editor', () => {
    let controller: EditorController | null = null;
    const changes: string[] = [];

    const make = (options: Partial<Parameters<typeof createEditor>[0]> = {}) => {
        document.body.innerHTML = '<div id="mount"></div>';
        changes.length = 0;
        controller = createEditor({ element: document.getElementById('mount')!, onChange: (value) => changes.push(value), ...options });
        return controller;
    };

    afterEach(() => {
        controller?.destroy();
        controller = null;
        vi.unstubAllGlobals();
    });

    it('marks the editable element as the contract\'s content part, in the prose look', () => {
        make({ content: '<p>Hi</p>' });
        const editable = document.querySelector('[contenteditable]')!;
        expect(editable.getAttribute('data-scope')).toBe('editor');
        expect(editable.getAttribute('data-part')).toBe('content');
        expect(editable.classList.contains('sigta-prose')).toBe(true);
    });

    it('reads and writes markdown, single newlines as breaks', () => {
        const editor = make({ format: 'markdown', content: '## What changed\n\n- **every** field\n\nLine one\nLine two' });
        expect(editor.editor.getHTML()).toContain('<h2>What changed</h2>');
        expect(editor.editor.getHTML()).toContain('<strong>every</strong>');
        expect(editor.editor.getHTML()).toContain('Line one<br>Line two');
        expect(editor.value()).toContain('## What changed');
        expect(editor.value()).toContain('- **every** field');
    });

    it('serializes an empty editor as nothing', () => {
        const editor = make({ content: '' });
        expect(editor.value()).toBe('');
    });

    it('runs the toolbar\'s commands and reports the change', () => {
        const editor = make({ content: '<p>Hello</p>' });
        editor.editor.commands.selectAll();
        editor.run('bold');
        expect(editor.isActive('bold')).toBe(true);
        expect(changes.at(-1)).toBe('<p><strong>Hello</strong></p>');
        editor.editor.commands.setTextSelection(2);
        editor.run('heading', { level: 2 });
        expect(editor.isActive('heading', { level: 2 })).toBe(true);
        editor.run('undo');
        expect(editor.isActive('heading', { level: 2 })).toBe(false);
    });

    it('links the selection, inserts a bare address, and unlinks', () => {
        const editor = make({ content: '<p>Read the docs</p>' });
        editor.editor.commands.selectAll();
        editor.applyLink(' https://sigta.dk ');
        expect(editor.value()).toContain('<a target="_blank" rel="noopener noreferrer nofollow" href="https://sigta.dk">Read the docs</a>');
        expect(editor.linkHref()).toBe('https://sigta.dk');
        editor.removeLink();
        expect(editor.value()).toBe('<p>Read the docs</p>');

        editor.editor.commands.setTextSelection(1);
        editor.applyLink('https://example.com');
        expect(editor.value()).toContain('>https://example.com</a>Read the docs');
    });

    it('replaces the content on purpose', () => {
        const editor = make({ format: 'markdown', content: 'Old' });
        editor.setContent('# New');
        expect(changes.at(-1)?.trim()).toBe('# New');
        editor.setContent('Quiet', false);
        expect(changes.at(-1)?.trim()).toBe('# New');
        expect(editor.value().trim()).toBe('Quiet');
    });

    it('uploads pictures through the host and inserts what it answers', async () => {
        const fetch = vi.fn(async () => new Response(JSON.stringify({ url: '/pictures/1.png' }), { status: 200 }));
        vi.stubGlobal('fetch', fetch);
        const counts: number[] = [];
        const editor = make({ format: 'markdown', content: 'Text', uploadUrl: '/upload', uploadToken: 'tok', onUploading: (n) => counts.push(n) });
        await editor.uploadImages([new File(['x'], 'dot.png', { type: 'image/png' })]);
        const [url, init] = fetch.mock.calls[0] as unknown as [string, RequestInit];
        expect(url).toBe('/upload');
        expect(init.method).toBe('POST');
        expect((init.headers as Record<string, string>)['X-CSRF-TOKEN']).toBe('tok');
        expect((init.body as FormData).get('file')).toBeInstanceOf(File);
        expect(editor.value()).toContain('![dot.png](/pictures/1.png)');
        expect(counts).toEqual([1, 0]);
    });

    it('warns and inserts nothing when an upload fails', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => new Response('{}', { status: 422 })));
        const notes: string[] = [];
        const editor = make({ content: '<p>Text</p>', uploadUrl: '/upload', notify: (text) => notes.push(text) });
        await editor.uploadImages([new File(['x'], 'dot.png', { type: 'image/png' })]);
        expect(notes).toEqual(['The picture could not be uploaded.']);
        expect(editor.value()).toBe('<p>Text</p>');
    });

    it('links a pasted internal address at once, then titles it from the resolver', async () => {
        const fetch = vi.fn(async () => new Response(JSON.stringify({ title: ' Kit: Kanban ', url: 'https://app.test/kanban' }), { status: 200 }));
        vi.stubGlobal('fetch', fetch);
        const editor = make({ format: 'markdown', content: '', resolveUrl: '/resolve?scope=kit' });
        await editor.pasteInternalLink('https://app.test/kit/kanban');
        expect(fetch.mock.calls[0][0]).toBe('/resolve?scope=kit&url=https%3A%2F%2Fapp.test%2Fkit%2Fkanban');
        expect(editor.value().trim()).toBe('[Kit: Kanban](https://app.test/kanban)');
    });

    it('leaves the address when the resolver does not know it', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => new Response('{}', { status: 404 })));
        expect(await resolveLink('/resolve', 'https://app.test/x')).toBeNull();
        const editor = make({ format: 'markdown', content: '', resolveUrl: '/resolve' });
        await editor.pasteInternalLink('https://app.test/x');
        expect(editor.value().trim()).toBe('[https://app.test/x](https://app.test/x)');
    });
});
