/* ---------------------------------------------------------------------------
   The rich text editor (TipTap), shared by both runtimes: the Livewire
   runtime's sigtaEditor (resources/js/editor.js, bundled on its own into
   dist/sigta-editor.js) and the Svelte Editor. Everything that is not Alpine
   or Svelte lives here — the extensions, markdown in and out, the toolbar
   and its commands, links, and pictures uploaded through the host — so both
   editors behave alike.

   Its own entry (@sigta-hq/core/editor), so TipTap only loads where an
   editor is used. SSR-safe: nothing touches the DOM until an editor is made.

   Keep what createEditor returns out of reactive state (Alpine data, Svelte
   $state): read back through a reactive proxy, ProseMirror objects come
   wrapped and fail their own identity checks ("Applying a mismatched
   transaction").
--------------------------------------------------------------------------- */
import { Editor, type Extensions } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import { Markdown } from '@tiptap/markdown';
import { Placeholder } from '@tiptap/extensions';
import type { Mark } from '@tiptap/pm/model';

export type EditorFormat = 'html' | 'markdown';

/** What the editor stores: markdown when asked, HTML otherwise. */
export const editorFormat = (value: unknown): EditorFormat => (value === 'markdown' ? 'markdown' : 'html');

export type EditorCommand =
    | 'bold' | 'italic' | 'strike' | 'link' | 'heading' | 'bulletList' | 'orderedList'
    | 'blockquote' | 'codeBlock' | 'image' | 'undo' | 'redo';

export interface EditorTool {
    command: EditorCommand;
    /** The pinned Lucide icon. */
    icon: string;
    /** Its accessible name. */
    label: string;
    attrs?: Record<string, unknown>;
}

/**
 * The formatting tools, in toolbar order (null is a separator); the picture
 * tool joins when the editor can upload. Undo and redo sit apart, at the end.
 */
export function editorTools(uploads: boolean): (EditorTool | null)[] {
    return [
        { command: 'bold', icon: 'bold', label: 'Bold' },
        { command: 'italic', icon: 'italic', label: 'Italic' },
        { command: 'strike', icon: 'strikethrough', label: 'Strikethrough' },
        { command: 'link', icon: 'link', label: 'Link' },
        null,
        { command: 'heading', icon: 'heading-2', label: 'Heading', attrs: { level: 2 } },
        { command: 'bulletList', icon: 'list', label: 'Bullet list' },
        { command: 'orderedList', icon: 'list-ordered', label: 'Numbered list' },
        { command: 'blockquote', icon: 'quote', label: 'Quote' },
        { command: 'codeBlock', icon: 'code', label: 'Code block' },
        ...(uploads ? [{ command: 'image', icon: 'image', label: 'Picture' } satisfies EditorTool] : []),
    ];
}

/** Like a textarea's rows: inline fields start at three lines, the form editor at six. */
export const editorRows = (rows: number | string | null | undefined, inline: boolean): number =>
    Math.trunc(Number(rows ?? (inline ? 3 : 6))) || 0;

/**
 * The id an editor bound to `name` (or wire:model) takes when none is given,
 * as Laravel's Str::slug makes it: a host re-render keys on it, so it must
 * not change between renders. Accents are dropped; letters Laravel would
 * transliterate further (ø, ß) are kept, which only matters for such names.
 */
export function bindingId(binding: string): string {
    const slug = binding
        .normalize('NFKD').replace(/[̀-ͯ]/g, '')
        .replace(/_+/g, '-')
        .replace(/@/g, '-at-')
        .toLowerCase()
        .replace(/[^-\p{L}\p{N}\s]+/gu, '')
        .replace(/[-\s]+/g, '-')
        .replace(/^-+|-+$/g, '');
    return `sigta-editor-${slug}`;
}

/** The attributes the editable element carries: the prose look and its contract part. */
export const EDITOR_CONTENT_ATTRIBUTES: Record<string, string> = {
    class: 'sigta-prose',
    'data-scope': 'editor',
    'data-part': 'content',
};

/** The TipTap extensions: the starter kit with links, pictures, the placeholder, and markdown when asked. */
export function editorExtensions(options: { format?: EditorFormat; placeholder?: string } = {}): Extensions {
    const extensions: Extensions = [
        StarterKit.configure({
            // A plain click places the caret; Cmd/Ctrl+click or the link tool
            // opens, so following a link never interrupts editing.
            link: { openOnClick: false, autolink: true, linkOnPaste: true, defaultProtocol: 'https' },
        }),
        // Pictures are block nodes with a real address: data URLs are refused so
        // a paste can never inline megabytes of base64 into the stored value.
        Image.configure({ inline: false, allowBase64: false }),
        Placeholder.configure({ placeholder: options.placeholder ?? '' }),
    ];
    if (options.format === 'markdown') {
        // Text written before markdown existed has bare newlines; treating them as
        // line breaks keeps its shape instead of collapsing it into one paragraph.
        extensions.push(Markdown.configure({ markedOptions: { gfm: true, breaks: true } }));
    }
    return extensions;
}

/**
 * The pasted text when it is exactly one absolute http(s) address on the
 * page's own origin, or null. Only the host app's own addresses can be given
 * a title.
 */
export function sameOriginUrl(text: string | null | undefined, base: { href: string; origin: string } = window.location): string | null {
    const trimmed = (text ?? '').trim();
    if (trimmed === '' || /\s/.test(trimmed)) return null;
    try {
        const url = new URL(trimmed, base.href);
        if (!/^https?:$/.test(url.protocol) || url.origin !== base.origin) return null;
        return url.href;
    } catch {
        return null;
    }
}

/** The pictures among dropped, pasted or picked files. */
export const imageFiles = (files: { files?: ArrayLike<File> | null } | null | undefined): File[] =>
    Array.from(files?.files ?? []).filter((file) => file.type.startsWith('image/'));

/** The CSRF token an upload sends: the editor's own, else the page's csrf-token meta. */
export function csrfToken(token: string | null | undefined): string {
    if (token) return token;
    if (typeof document === 'undefined') return '';
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

/**
 * Posts one picture to the host's endpoint as multipart `file`, with the
 * CSRF token; the endpoint answers JSON { url, name? }.
 */
export async function uploadImage(file: File, url: string, token?: string | null): Promise<{ url: string; name: string }> {
    const body = new FormData();
    body.append('file', file, file.name);
    const response = await fetch(url, {
        method: 'POST',
        body,
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': csrfToken(token),
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
    });
    if (!response.ok) throw new Error(`Upload failed with status ${response.status}`);
    const answer = (await response.json()) as { url?: string; name?: string };
    if (!answer.url) throw new Error('Upload returned no address');
    return { url: answer.url, name: answer.name ?? file.name };
}

/**
 * Asks the host's resolver what an address of its own points at:
 * GET resolve-url?url=… answering JSON { title, url? }. Null when it does
 * not know (or does not answer).
 */
export async function resolveLink(resolveUrl: string, url: string): Promise<{ title: string; url: string } | null> {
    try {
        const separator = resolveUrl.includes('?') ? '&' : '?';
        const response = await fetch(`${resolveUrl}${separator}url=${encodeURIComponent(url)}`, {
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!response.ok) return null;
        const { title, url: current } = (await response.json()) as { title?: unknown; url?: unknown };
        if (typeof title !== 'string' || title.trim() === '') return null;
        return { title: title.trim(), url: typeof current === 'string' && current !== '' ? current : url };
    } catch {
        return null;
    }
}

/** Raises a warning toast (through window.Sigta when the runtime is there). */
export function notifyWarning(text: string): void {
    const sigta = (window as { Sigta?: { toast?: (text: string, options: Record<string, unknown>) => void } }).Sigta;
    if (sigta?.toast) sigta.toast(text, { variant: 'warning' });
    else window.dispatchEvent(new CustomEvent('sigta-toast', { detail: { text, variant: 'warning' } }));
}

/** Opens an address in a new tab, the way the editor follows links. */
export function openLink(href: string): void {
    const trimmed = href.trim();
    if (trimmed !== '') window.open(trimmed, '_blank', 'noopener');
}

/** Cmd/Ctrl+click on a link inside the editable area follows it in a new tab. True when it did. */
export function followLink(event: MouseEvent): boolean {
    if (!(event.metaKey || event.ctrlKey)) return false;
    const href = (event.target as Element | null)?.closest?.('a')?.getAttribute('href');
    if (!href) return false;
    event.preventDefault();
    window.open(href, '_blank', 'noopener');
    return true;
}

/** Cmd+K or Ctrl+K (and no other modifier): the link tool's shortcut. */
export const isLinkShortcut = (event: KeyboardEvent): boolean =>
    event.key.toLowerCase() === 'k' && !event.altKey && !event.shiftKey && event.metaKey !== event.ctrlKey;

/* -------- the editor --------------------------------------------------------- */

export interface EditorOptions {
    /** Where TipTap mounts; it owns this element's subtree from then on. */
    element: HTMLElement;
    /** The initial content, in the format. */
    content?: string;
    format?: EditorFormat;
    placeholder?: string;
    /** The host's picture endpoint; without it pictures are not taken. */
    uploadUrl?: string | null;
    uploadToken?: string | null;
    /** The warning a failed upload shows. */
    uploadError?: string | null;
    /** The host's link resolver; without it pasted addresses stay as typed. */
    resolveUrl?: string | null;
    /** The content changed (serialized in the format; empty when the editor is). */
    onChange?: (value: string) => void;
    /** Any transaction (selection included): toolbar states may have changed. */
    onTransaction?: () => void;
    onFocus?: () => void;
    onBlur?: () => void;
    /** How many pictures are uploading. */
    onUploading?: (count: number) => void;
    notify?: (text: string) => void;
}

export interface EditorController {
    readonly editor: Editor;
    readonly format: EditorFormat;
    /** The content in the format ('' when empty). */
    value(): string;
    /** Replaces the content on purpose (a restored draft, another record). */
    setContent(value: string | null | undefined, emitUpdate?: boolean): void;
    isActive(nameOrAttrs: string | Record<string, unknown>, attrs?: Record<string, unknown>): boolean;
    /** Runs a formatting command, undo or redo (the link and picture tools open their own UI). */
    run(command: EditorCommand, attrs?: Record<string, unknown>): void;
    /** The address of the link at the caret ('' outside one). */
    linkHref(): string;
    /** Links the selection (or inserts the address as linked text); an empty address unlinks. */
    applyLink(href: string): void;
    removeLink(): void;
    focus(): void;
    uploadImages(files: File[], position?: number): Promise<void>;
    pasteInternalLink(url: string): Promise<void>;
    retitleLinks(href: string, title: string, currentHref: string): void;
    destroy(): void;
}

export function createEditor(options: EditorOptions): EditorController {
    const format = options.format ?? 'html';
    const markdown = format === 'markdown';
    let uploading = 0;
    const setUploading = (delta: number) => {
        uploading += delta;
        options.onUploading?.(uploading);
    };
    const notify = options.notify ?? notifyWarning;

    const serialize = (editor: Editor): string => {
        if (editor.isEmpty) return '';
        return markdown ? editor.getMarkdown() : editor.getHTML();
    };

    const editor: Editor = new Editor({
        element: options.element,
        extensions: editorExtensions({ format, placeholder: options.placeholder }),
        content: options.content ?? '',
        contentType: markdown ? 'markdown' : 'html',
        editorProps: {
            // The editable element fills the editor's box (its padding comes from the
            // contract), making every part of the box a place to click.
            attributes: { ...EDITOR_CONTENT_ATTRIBUTES },
            // A picture dropped into the text is uploaded through the host's endpoint
            // and lands where it was dropped; anything else drops as usual.
            handleDrop: (view, event, _slice, moved) => {
                if (moved || !options.uploadUrl) return false;
                const files = imageFiles(event.dataTransfer);
                if (files.length === 0) return false;
                event.preventDefault();
                void controller.uploadImages(files, view.posAtCoords({ left: event.clientX, top: event.clientY })?.pos);
                return true;
            },
            // Pasted pictures upload the same way. A pasted address on the page's own
            // origin is linked at once and then asks the host's resolver for its
            // title; with text selected the link tool wraps the selection as before.
            handlePaste: (view, event) => {
                const files = imageFiles(event.clipboardData);
                if (files.length > 0 && options.uploadUrl) {
                    event.preventDefault();
                    void controller.uploadImages(files);
                    return true;
                }
                if (!options.resolveUrl || !view.state.selection.empty) return false;
                const url = sameOriginUrl(event.clipboardData?.getData('text/plain'));
                if (!url) return false;
                event.preventDefault();
                void controller.pasteInternalLink(url);
                return true;
            },
        },
        onTransaction: () => options.onTransaction?.(),
        onFocus: () => options.onFocus?.(),
        onBlur: () => options.onBlur?.(),
        onUpdate: ({ editor: current }) => options.onChange?.(serialize(current)),
    });

    const controller: EditorController = {
        editor,
        format,
        value: () => serialize(editor),
        setContent(value, emitUpdate = true) {
            editor.commands.setContent(value ?? '', { contentType: format, emitUpdate });
        },
        isActive(nameOrAttrs, attrs) {
            return typeof nameOrAttrs === 'string' ? editor.isActive(nameOrAttrs, attrs) : editor.isActive(nameOrAttrs);
        },
        run(command, attrs) {
            const chain = editor.chain().focus();
            switch (command) {
                case 'bold': chain.toggleBold().run(); break;
                case 'italic': chain.toggleItalic().run(); break;
                case 'strike': chain.toggleStrike().run(); break;
                case 'heading': chain.toggleHeading((attrs as { level: 1 | 2 | 3 | 4 | 5 | 6 } | undefined) ?? { level: 2 }).run(); break;
                case 'bulletList': chain.toggleBulletList().run(); break;
                case 'orderedList': chain.toggleOrderedList().run(); break;
                case 'blockquote': chain.toggleBlockquote().run(); break;
                case 'codeBlock': chain.toggleCodeBlock().run(); break;
                case 'undo': chain.undo().run(); break;
                case 'redo': chain.redo().run(); break;
            }
        },
        linkHref: () => (editor.getAttributes('link').href as string | undefined) ?? '',
        applyLink(href) {
            const address = href.trim();
            const chain = editor.chain().focus().extendMarkRange('link');
            if (address === '') {
                chain.unsetLink().run();
            } else if (editor.state.selection.empty && !editor.isActive('link')) {
                // Nothing to wrap, so the address itself becomes the linked text.
                chain.insertContent({ type: 'text', text: address, marks: [{ type: 'link', attrs: { href: address } }] }).run();
            } else {
                chain.setLink({ href: address }).run();
            }
        },
        removeLink() {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
        },
        focus() {
            editor.commands.focus();
        },

        // Each file is posted to the host's endpoint and inserted as a picture once
        // the endpoint answers with its address. A failure tells the member and
        // inserts nothing, so the text never carries a broken picture.
        async uploadImages(files, position) {
            const url = options.uploadUrl;
            if (!url) return;
            for (const file of files) {
                setUploading(1);
                try {
                    const uploaded = await uploadImage(file, url, options.uploadToken);
                    const content = { type: 'image', attrs: { src: uploaded.url, alt: uploaded.name } };
                    const chain = editor.chain().focus();
                    (position === undefined ? chain.insertContent(content) : chain.insertContentAt(position, content)).run();
                    position = undefined;
                } catch {
                    notify(options.uploadError || 'The picture could not be uploaded.');
                } finally {
                    setUploading(-1);
                }
            }
        },

        // An internal address is linked the moment it is pasted, then the host's
        // resolver is asked what it points at; an address it does not know stays
        // as typed.
        async pasteInternalLink(url) {
            const resolveUrl = options.resolveUrl;
            if (!resolveUrl) return;
            editor.chain().focus().insertContent({ type: 'text', text: url, marks: [{ type: 'link', attrs: { href: url } }] }).run();
            const resolved = await resolveLink(resolveUrl, url);
            if (resolved) controller.retitleLinks(url, resolved.title, resolved.url);
        },

        // Every link in the text that still shows the address as its own text is
        // given the title (and the current address, in case the resolver knows a
        // newer one).
        retitleLinks(href, title, currentHref) {
            if (editor.isDestroyed) return;
            const linkType = editor.schema.marks.link;
            const found: { from: number; to: number; marks: readonly Mark[]; mark: Mark }[] = [];

            editor.state.doc.descendants((node, pos) => {
                if (!node.isText) return;
                const mark = node.marks.find((candidate) => candidate.type === linkType && candidate.attrs.href === href);
                if (mark && node.text === href) found.push({ from: pos, to: pos + node.nodeSize, marks: node.marks, mark });
            });

            if (found.length === 0) return;

            // Later matches first, so earlier positions stay valid while replacing.
            let transaction = editor.state.tr;
            found.reverse().forEach(({ from, to, marks, mark }) => {
                const retitled = linkType.create({ ...mark.attrs, href: currentHref });
                const others = marks.filter((candidate) => candidate !== mark);
                transaction = transaction.replaceWith(from, to, editor.schema.text(title, [retitled, ...others]));
            });
            editor.view.dispatch(transaction);
        },

        destroy() {
            editor.destroy();
        },
    };

    return controller;
}
