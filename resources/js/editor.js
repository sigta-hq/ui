/* ---------------------------------------------------------------------------
   Sigta UI — rich text editor runtime (TipTap)

   Bundled separately (dist/sigta-editor.js) and only loaded on pages using
   <x-sigta::editor> via the @sigtaEditorScripts directive, so the core
   runtime stays tiny. The editor itself — extensions, markdown, the toolbar's
   commands, links and picture uploads — is @sigta-hq/core's editor, shared
   with the Svelte Editor; this file is only its Alpine glue.
--------------------------------------------------------------------------- */
import { createEditor, editorFormat, followLink, imageFiles, openLink } from '../../packages/core/src/editor.ts';

// Editors by component root, kept out of Alpine's data on purpose: a property
// read on reactive data hands back a proxy, and proxied ProseMirror objects
// fail their own identity checks ("Applying a mismatched transaction").
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
        uploading: 0,

        controller() {
            return instances.get(this.$root) ?? null;
        },
        instance() {
            return this.controller()?.editor ?? null;
        },

        init() {
            const data = this.$el.dataset;
            this.format = editorFormat(data.format);
            instances.set(this.$root, createEditor({
                element: this.$refs.element,
                content: this.$refs.value?.value ?? '',
                format: this.format,
                placeholder: data.placeholder ?? '',
                uploadUrl: data.uploadUrl || null,
                uploadToken: data.uploadToken ?? null,
                uploadError: data.uploadError || null,
                resolveUrl: data.resolveUrl || null,
                onTransaction: () => { this.state++; },
                onFocus: () => { this.focused = true; },
                onBlur: () => { this.focused = false; },
                onChange: (value) => this.publish(value),
                onUploading: (count) => { this.uploading = count; },
            }));
        },
        destroy() {
            this.controller()?.destroy();
            instances.delete(this.$root);
        },

        // The hidden input is write-only from the editor's side: a host re-render
        // that rewrites it can never disturb what the user is typing.
        publish(value) {
            const input = this.$refs.value;
            if (!input) return;
            input.value = value;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        },
        // Deliberate replacement (restoring a draft, switching records) goes through
        // here, so hosts never have to reach into the editor or the hidden input.
        setContent(value = '') {
            this.controller()?.setContent(value);
        },

        // Toolbar helpers — `state` is referenced so Alpine tracks reactivity.
        isActive(nameOrAttrs, attrs = undefined) {
            void this.state;
            return this.controller()?.isActive(nameOrAttrs, attrs) ?? false;
        },
        run(command, attrs = undefined) {
            if (command === 'link') return this.openLink();
            if (command === 'image') return this.pickImage();
            this.controller()?.run(command, attrs);
        },

        // Pictures: the toolbar button opens the native picker; drop and paste reach
        // the editor directly.
        pickImage() {
            this.$refs.imageInput?.click();
        },
        pickedImages(event) {
            const files = imageFiles(event.target);
            event.target.value = '';
            if (files.length > 0) this.controller()?.uploadImages(files);
        },

        // Cmd/Ctrl+click on a link inside the editable area follows it in a new tab.
        followLink(event) {
            followLink(event);
        },
        visitLink() {
            openLink(this.linkUrl);
        },
        openLink() {
            const controller = this.controller();
            if (!controller) return;
            this.linkUrl = controller.linkHref();
            this.linkOpen = true;
            this.$nextTick(() => this.$refs.linkInput?.focus());
        },
        applyLink() {
            const controller = this.controller();
            if (!controller) return;
            controller.applyLink(this.linkUrl);
            this.closeLink();
        },
        removeLink() {
            this.controller()?.removeLink();
            this.closeLink();
        },
        closeLink() {
            this.linkOpen = false;
            this.linkUrl = '';
            this.controller()?.focus();
        },
    }));
}

if (window.Alpine) {
    register();
} else {
    document.addEventListener('alpine:init', register);
}
