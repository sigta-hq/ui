/* ---------------------------------------------------------------------------
   Sigta UI — rich text editor runtime (TipTap)

   Bundled separately (dist/sigta-editor.js) and only loaded on pages using
   <x-sigta::editor> via the @sigtaEditorScripts directive, so the core
   runtime stays tiny.
--------------------------------------------------------------------------- */
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

function register() {
    const Alpine = window.Alpine;
    if (!Alpine || Alpine.__sigtaEditorRegistered) return;
    Alpine.__sigtaEditorRegistered = true;

    Alpine.data('sigtaEditor', (initial = '') => ({
        editor: null,
        state: 0, // bumped on transactions so toolbar bindings re-evaluate

        init() {
            this.editor = new Editor({
                element: this.$refs.element,
                extensions: [StarterKit],
                content: initial,
                editorProps: {
                    attributes: { class: 'sigta-prose focus:outline-none' },
                },
                onTransaction: () => { this.state++; },
                onUpdate: ({ editor }) => {
                    const input = this.$refs.value;
                    if (!input) return;
                    input.value = editor.getHTML();
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                },
            });
        },
        destroy() {
            this.editor?.destroy();
        },

        // Toolbar helpers — `state` is referenced so Alpine tracks reactivity.
        isActive(nameOrAttrs, attrs = undefined) {
            void this.state;
            return this.editor?.isActive(nameOrAttrs, attrs) ?? false;
        },
        run(command, attrs = undefined) {
            const chain = this.editor?.chain().focus();
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
    }));
}

if (window.Alpine) {
    register();
} else {
    document.addEventListener('alpine:init', register);
}
