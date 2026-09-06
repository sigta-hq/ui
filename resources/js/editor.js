/* ---------------------------------------------------------------------------
   Sigta UI — rich text editor runtime (TipTap)

   Bundled separately (dist/sigta-editor.js) and only loaded on pages using
   <x-sigta::editor> via the @sigtaEditorScripts directive, so the core
   runtime stays tiny.
--------------------------------------------------------------------------- */
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import { Markdown } from '@tiptap/markdown';
import { Placeholder } from '@tiptap/extensions';

// TipTap instances by component root, kept out of Alpine's data on purpose: a
// property read on reactive data hands back a proxy, and proxied ProseMirror
// objects fail their own identity checks ("Applying a mismatched transaction").
// A method call is the one access the reactive proxy cannot wrap.
const instances = new WeakMap();

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

        instance() {
            return instances.get(this.$root) ?? null;
        },

        init() {
            this.format = this.$el.dataset.format === 'markdown' ? 'markdown' : 'html';
            const initial = this.$refs.value?.value ?? '';
            const markdown = this.format === 'markdown';
            const extensions = [
                StarterKit.configure({
                    // A plain click places the caret; Cmd/Ctrl+click or the link tool
                    // opens, so following a link never interrupts editing.
                    link: { openOnClick: false, autolink: true, linkOnPaste: true, defaultProtocol: 'https' },
                }),
                Placeholder.configure({ placeholder: this.$el.dataset.placeholder ?? '' }),
            ];

            if (markdown) {
                // Text written before markdown existed has bare newlines; treating them as
                // line breaks keeps its shape instead of collapsing it into one paragraph.
                extensions.push(Markdown.configure({ markedOptions: { gfm: true, breaks: true } }));
            }

            instances.set(this.$root, new Editor({
                element: this.$refs.element,
                extensions,
                content: initial,
                contentType: markdown ? 'markdown' : 'html',
                editorProps: {
                    attributes: { class: 'sigta-prose focus:outline-none' },
                },
                onTransaction: () => { this.state++; },
                onFocus: () => { this.focused = true; },
                onBlur: () => { this.focused = false; },
                onUpdate: ({ editor }) => this.publish(editor),
            }));
        },
        destroy() {
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
