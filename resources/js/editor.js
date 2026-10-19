/* ---------------------------------------------------------------------------
   Sigta UI — rich text editor runtime (TipTap)

   Bundled separately (dist/sigta-editor.js) and only loaded on pages using
   <x-sigta::editor> via the @sigtaEditorScripts directive, so the core
   runtime stays tiny.
--------------------------------------------------------------------------- */
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Mention from '@tiptap/extension-mention';
import { Markdown } from '@tiptap/markdown';
import { Placeholder } from '@tiptap/extensions';

// TipTap instances by component root, kept out of Alpine's data on purpose: a
// property read on reactive data hands back a proxy, and proxied ProseMirror
// objects fail their own identity checks ("Applying a mismatched transaction").
// A method call is the one access the reactive proxy cannot wrap.
const instances = new WeakMap();

// The open mention suggestion by component root, for the same reason: its
// command and its positioning hold the editor, so they stay out of Alpine's data.
const suggestions = new WeakMap();

// The pasted text when it is exactly one absolute http(s) address on the page's
// own origin, or null. Only the host app's own addresses can be given a title.
const sameOriginUrl = (text) => {
    const trimmed = (text ?? '').trim();
    if (trimmed === '' || /\s/.test(trimmed)) return null;
    try {
        const url = new URL(trimmed, window.location.href);
        if (!/^https?:$/.test(url.protocol) || url.origin !== window.location.origin) return null;
        return url.href;
    } catch (error) {
        return null;
    }
};

const imageFiles = (dataTransfer) => Array.from(dataTransfer?.files ?? []).filter((file) => file.type.startsWith('image/'));

function register() {
    const Alpine = window.Alpine;
    if (!Alpine || Alpine.__sigtaEditorRegistered) return;
    Alpine.__sigtaEditorRegistered = true;

    // Initial content and options come from the DOM (the hidden input's value and
    // the root's data attributes) rather than x-data arguments: a Livewire morph
    // that changed the x-data string would make Alpine rebuild the editor.
    Alpine.data('sigtaEditor', () => ({
        state: 0, // bumped on transactions so toolbar bindings re-evaluate
        focused: false,
        linkOpen: false,
        linkUrl: '',
        format: 'html',
        uploading: 0,
        mentionItems: [],
        mentionActive: 0,

        instance() {
            return instances.get(this.$root) ?? null;
        },

        init() {
            this.format = this.$el.dataset.format === 'markdown' ? 'markdown' : 'html';
            const initial = this.$refs.value?.value ?? '';
            const markdown = this.format === 'markdown';
            const uploadUrl = this.$el.dataset.uploadUrl || null;
            const resolveUrl = this.$el.dataset.resolveUrl || null;
            const mentionUrl = this.$el.dataset.mentionUrl || null;
            const extensions = [
                StarterKit.configure({
                    // A plain click places the caret; Cmd/Ctrl+click or the link tool
                    // opens, so following a link never interrupts editing.
                    link: { openOnClick: false, autolink: true, linkOnPaste: true, defaultProtocol: 'https' },
                }),
                // Pictures are block nodes with a real address: data URLs are refused so
                // a paste can never inline megabytes of base64 into the stored value.
                Image.configure({ inline: false, allowBase64: false }),
                Placeholder.configure({ placeholder: this.$el.dataset.placeholder ?? '' }),
            ];

            if (markdown) {
                // Text written before markdown existed has bare newlines; treating them as
                // line breaks keeps its shape instead of collapsing it into one paragraph.
                extensions.push(Markdown.configure({ markedOptions: { gfm: true, breaks: true } }));
            }

            if (mentionUrl) {
                extensions.push(this.mentions(mentionUrl));
            }

            instances.set(this.$root, new Editor({
                element: this.$refs.element,
                extensions,
                content: initial,
                contentType: markdown ? 'markdown' : 'html',
                editorProps: {
                    // The mount's padding classes land on the editable element so it fills
                    // the box (see core.css), making every part of the box a place to click.
                    attributes: { class: `sigta-prose focus:outline-none ${this.$refs.element?.dataset.contentClass ?? ''}`.trim() },
                    // A picture dropped into the text is uploaded through the host's endpoint
                    // and lands where it was dropped; anything else drops as usual.
                    handleDrop: (view, event, slice, moved) => {
                        if (moved || !uploadUrl) return false;
                        const files = imageFiles(event.dataTransfer);
                        if (files.length === 0) return false;
                        event.preventDefault();
                        this.uploadImages(files, view.posAtCoords({ left: event.clientX, top: event.clientY })?.pos);
                        return true;
                    },
                    // Pasted pictures upload the same way. A pasted address on the page's own
                    // origin is linked at once and then asks the host's resolver for its
                    // title; with text selected the link tool wraps the selection as before.
                    handlePaste: (view, event) => {
                        const files = imageFiles(event.clipboardData);
                        if (files.length > 0 && uploadUrl) {
                            event.preventDefault();
                            this.uploadImages(files);
                            return true;
                        }
                        if (!resolveUrl || !view.state.selection.empty) return false;
                        const url = sameOriginUrl(event.clipboardData?.getData('text/plain'));
                        if (!url) return false;
                        event.preventDefault();
                        this.pasteInternalLink(url);
                        return true;
                    },
                },
                onTransaction: () => { this.state++; },
                onFocus: () => { this.focused = true; },
                onBlur: () => { this.focused = false; },
                onUpdate: ({ editor }) => this.publish(editor),
            }));
        },
        destroy() {
            this.hideMentions();
            this.instance()?.destroy();
            instances.delete(this.$root);
        },

        // The hidden input is write-only from the editor's side: a host re-render
        // that rewrites it can never disturb what the user is typing.
        publish(editor) {
            const input = this.$refs.value;
            if (!input) return;
            input.value = this.serialize(editor);
            input.dispatchEvent(new Event('input', { bubbles: true }));
        },
        serialize(editor) {
            if (editor.isEmpty) return '';
            return this.format === 'markdown' ? editor.getMarkdown() : editor.getHTML();
        },
        // Deliberate replacement (restoring a draft, switching records) goes through
        // here, so hosts never have to reach into the editor or the hidden input.
        setContent(value = '') {
            this.instance()?.commands.setContent(value ?? '', { contentType: this.format, emitUpdate: true });
        },

        // Toolbar helpers — `state` is referenced so Alpine tracks reactivity.
        isActive(nameOrAttrs, attrs = undefined) {
            void this.state;
            return this.instance()?.isActive(nameOrAttrs, attrs) ?? false;
        },
        run(command, attrs = undefined) {
            if (command === 'link') return this.openLink();
            if (command === 'image') return this.pickImage();
            const chain = this.instance()?.chain().focus();
            if (!chain) return;
            switch (command) {
                case 'bold': chain.toggleBold().run(); break;
                case 'italic': chain.toggleItalic().run(); break;
                case 'strike': chain.toggleStrike().run(); break;
                case 'heading': chain.toggleHeading(attrs ?? { level: 2 }).run(); break;
                case 'bulletList': chain.toggleBulletList().run(); break;
                case 'orderedList': chain.toggleOrderedList().run(); break;
                case 'blockquote': chain.toggleBlockquote().run(); break;
                case 'codeBlock': chain.toggleCodeBlock().run(); break;
                case 'undo': chain.undo().run(); break;
                case 'redo': chain.redo().run(); break;
            }
        },

        // Pictures: the toolbar button opens the native picker; drop and paste come
        // through the editor props above. Each file is posted to the host's upload
        // endpoint with the session's CSRF token and inserted as an image once the
        // endpoint answers with its address. A failure tells the member and inserts
        // nothing, so the text never carries a broken picture.
        pickImage() {
            this.$refs.imageInput?.click();
        },
        pickedImages(event) {
            const files = Array.from(event.target.files ?? []).filter((file) => file.type.startsWith('image/'));
            event.target.value = '';
            if (files.length > 0) this.uploadImages(files);
        },
        async uploadImages(files, position = undefined) {
            const editor = this.instance();
            const uploadUrl = this.$el.dataset.uploadUrl;
            if (!editor || !uploadUrl) return;

            for (const file of files) {
                this.uploading++;
                try {
                    const body = new FormData();
                    body.append('file', file, file.name);
                    const response = await fetch(uploadUrl, {
                        method: 'POST',
                        body,
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': this.$el.dataset.uploadToken ?? '',
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                        },
                    });
                    if (!response.ok) throw new Error(`Upload failed with status ${response.status}`);
                    const { url, name } = await response.json();
                    if (!url) throw new Error('Upload returned no address');
                    const content = { type: 'image', attrs: { src: url, alt: name ?? file.name } };
                    const chain = editor.chain().focus();
                    (position === undefined ? chain.insertContent(content) : chain.insertContentAt(position, content)).run();
                    position = undefined;
                } catch (error) {
                    this.notify(this.$el.dataset.uploadError || 'The picture could not be uploaded.');
                } finally {
                    this.uploading--;
                }
            }
        },
        notify(text) {
            if (window.Sigta?.toast) {
                window.Sigta.toast(text, { variant: 'warning' });
            } else {
                window.dispatchEvent(new CustomEvent('sigta-toast', { detail: { text, variant: 'warning' } }));
            }
        },

        // An internal address is linked the moment it is pasted, then the host's
        // resolver is asked what it points at. When it answers, every link in the
        // text that still shows that address as its own text is given the title
        // (and the current address, in case the resolver knows a newer one); an
        // address the resolver does not know stays as typed.
        async pasteInternalLink(url) {
            const editor = this.instance();
            const resolveUrl = this.$el.dataset.resolveUrl;
            if (!editor || !resolveUrl) return;

            editor.chain().focus().insertContent({ type: 'text', text: url, marks: [{ type: 'link', attrs: { href: url } }] }).run();

            try {
                const separator = resolveUrl.includes('?') ? '&' : '?';
                const response = await fetch(`${resolveUrl}${separator}url=${encodeURIComponent(url)}`, {
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) return;
                const { title, url: current } = await response.json();
                if (typeof title !== 'string' || title.trim() === '') return;
                this.retitleLinks(url, title.trim(), typeof current === 'string' && current !== '' ? current : url);
            } catch (error) {
                // The address stays as typed; nothing to recover.
            }
        },
        retitleLinks(href, title, currentHref) {
            const editor = this.instance();
            if (!editor) return;
            const linkType = editor.schema.marks.link;
            const found = [];

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

        // Mentions: typing @ asks the host's endpoint what matches the words after
        // it and lists the answers beside the caret. An answer with an address is
        // inserted as a link titled with its label; any other is a mention node
        // carrying its id, so the host can tell who was named however they are
        // called later. Names and titles have spaces, so the query runs across
        // them, and the list stays out of the way while nothing matches.
        mentions(mentionUrl) {
            return Mention.configure({
                HTMLAttributes: { class: 'sigta-mention' },
                suggestion: {
                    char: '@',
                    allowSpaces: true,
                    debounce: 120,
                    offset: { mainAxis: 6 },
                    // Fixed, so the list escapes a scrolling or clipping box around the editor.
                    floatingUi: { strategy: 'fixed' },
                    items: ({ query, signal }) => this.findMentions(mentionUrl, query, signal),
                    command: ({ editor, range, props }) => this.insertMention(editor, range, props),
                    render: () => ({
                        onStart: (props) => this.showMentions(props),
                        onUpdate: (props) => this.showMentions(props),
                        onKeyDown: ({ event }) => this.mentionKey(event),
                        onExit: () => this.hideMentions(),
                    }),
                },
            });
        },
        async findMentions(mentionUrl, query, signal) {
            const held = suggestions.get(this.$root);

            // Words added after a query that matched nothing cannot match either, so
            // a sentence typed on after a stray @ asks the host nothing more.
            if (held?.emptyQuery !== undefined && query.startsWith(held.emptyQuery)) return [];

            const separator = mentionUrl.includes('?') ? '&' : '?';
            const response = await fetch(`${mentionUrl}${separator}q=${encodeURIComponent(query)}`, {
                credentials: 'same-origin',
                signal,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) return [];
            const answer = await response.json();
            const items = (Array.isArray(answer) ? answer : []).filter(
                (item) => item && typeof item.label === 'string' && item.label.trim() !== '' && (item.url || (item.id !== undefined && item.id !== null)),
            );

            if (items.length === 0 && held) held.emptyQuery = query;

            return items;
        },
        showMentions(props) {
            const held = suggestions.get(this.$root) ?? {};
            held.command = props.command;
            if (!held.unmount && this.$refs.mentionPanel) held.unmount = props.mount(this.$refs.mentionPanel);
            suggestions.set(this.$root, held);

            // While the next answer loads the last one stays up, so the list never blinks.
            if (props.loading) return;
            this.mentionItems = props.items;
            this.mentionActive = 0;
        },
        hideMentions() {
            suggestions.get(this.$root)?.unmount?.();
            suggestions.delete(this.$root);
            this.mentionItems = [];
            this.mentionActive = 0;
        },
        mentionKey(event) {
            const count = this.mentionItems.length;
            if (count === 0) return false;

            switch (event.key) {
                case 'ArrowDown':
                    this.mentionActive = (this.mentionActive + 1) % count;
                    this.$nextTick(() => this.$refs.mentionPanel?.querySelector('[data-active]')?.scrollIntoView({ block: 'nearest' }));
                    return true;
                case 'ArrowUp':
                    this.mentionActive = (this.mentionActive - 1 + count) % count;
                    this.$nextTick(() => this.$refs.mentionPanel?.querySelector('[data-active]')?.scrollIntoView({ block: 'nearest' }));
                    return true;
                case 'Enter':
                case 'Tab':
                    this.chooseMention(this.mentionActive);
                    return true;
                default:
                    return false;
            }
        },
        chooseMention(index) {
            const item = this.mentionItems[index];
            const command = suggestions.get(this.$root)?.command;
            if (!item || !command) return;
            command({ id: item.id, label: item.label, url: item.url });
        },
        insertMention(editor, range, item) {
            // A space already after the caret is reused, so choosing never leaves two.
            if (editor.view.state.selection.$to.nodeAfter?.text?.startsWith(' ')) range.to += 1;

            const label = item.label.trim();
            const content = item.url
                ? { type: 'text', text: label, marks: [{ type: 'link', attrs: { href: item.url } }] }
                // The label is written into the markdown shortcode, which has no escape for these.
                : { type: 'mention', attrs: { id: String(item.id), label: label.replace(/["\]]/g, '') } };

            editor.chain().focus().insertContentAt(range, [content, { type: 'text', text: ' ' }]).run();
            editor.view.dom.ownerDocument.defaultView?.getSelection()?.collapseToEnd();
        },

        // Cmd/Ctrl+click on a link inside the editable area follows it in a new tab.
        followLink(event) {
            if (!(event.metaKey || event.ctrlKey)) return;
            const href = event.target.closest('a')?.getAttribute('href');
            if (!href) return;
            event.preventDefault();
            window.open(href, '_blank', 'noopener');
        },
        visitLink() {
            const href = this.linkUrl.trim();
            if (href !== '') window.open(href, '_blank', 'noopener');
        },
        openLink() {
            const editor = this.instance();
            if (!editor) return;
            this.linkUrl = editor.getAttributes('link').href ?? '';
            this.linkOpen = true;
            this.$nextTick(() => this.$refs.linkInput?.focus());
        },
        applyLink() {
            const editor = this.instance();
            if (!editor) return;
            const href = this.linkUrl.trim();
            const chain = editor.chain().focus().extendMarkRange('link');

            if (href === '') {
                chain.unsetLink().run();
            } else if (editor.state.selection.empty && !editor.isActive('link')) {
                // Nothing to wrap, so the address itself becomes the linked text.
                chain.insertContent({ type: 'text', text: href, marks: [{ type: 'link', attrs: { href } }] }).run();
            } else {
                chain.setLink({ href }).run();
            }

            this.closeLink();
        },
        removeLink() {
            this.instance()?.chain().focus().extendMarkRange('link').unsetLink().run();
            this.closeLink();
        },
        closeLink() {
            this.linkOpen = false;
            this.linkUrl = '';
            this.instance()?.commands.focus();
        },
    }));
}

if (window.Alpine) {
    register();
} else {
    document.addEventListener('alpine:init', register);
}
