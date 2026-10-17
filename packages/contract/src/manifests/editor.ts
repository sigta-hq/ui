import { defineComponent } from '../schema.ts';
import { c, el } from '../examples.ts';

const brief = '<h2>Written by hand</h2><p>We design and build software the <strong>careful</strong> way, then keep the best of it as <em>products we run ourselves</em>.</p><ul><li>Researched</li><li>Drawn</li><li>Tested</li></ul>';
const notes = '## What changed\n\n- Autosave for **every** field\n- Links such as [the docs](https://sigta.dk)\n\nLine one\nLine two keeps its break.';
const content = '[data-scope="editor"][data-part="content"]';

export default defineComponent({
    name: 'editor',
    title: 'Editor',
    tier: 'components',
    page: 'editor',
    svelte: 'Editor',
    description: 'Rich text editor (TipTap) on paper: a toolbar of square tools (bold, italic, strike, link, heading, lists, quote, code, a picture tool with uploads, undo and redo) over an editable page in the prose look. Stores HTML, or markdown with format="markdown"; variant="inline" is the always-editable, borderless-until-hover form for detail views.',
    aria: 'The toolbar is role="toolbar" (aria-label "Formatting", aria-controls the editable region); each tool is a labelled button with aria-pressed while its mark or block is at the caret; the upload indicator is a role="status" live region. Links open with Cmd/Ctrl+K.',
    props: {
        label: { type: 'string', default: null, description: 'Label above the editor.' },
        description: { type: 'string', default: null, description: 'Help text under it.' },
        error: { type: 'string', default: null, description: 'Error text; turns the frame red.' },
        name: { type: 'string', default: null, description: 'Form name of the hidden value input; without an id, the editor\'s stable id derives from it.' },
        value: {
            type: 'string',
            default: '',
            bindable: true,
            description: 'The content, in the format.',
            blade: { note: 'The initial content; wire:model on the component binds the hidden input.' },
            svelte: { note: 'bind:value; a value set from outside replaces the content.' },
        },
        format: { type: 'enum', values: ['html', 'markdown'], default: 'html', description: 'What the value holds: HTML, or markdown (single newlines are line breaks). Anything else is html.' },
        variant: { type: 'enum', values: ['default', 'inline'], default: 'default', description: 'default (framed, for forms) or inline (borderless until hover, toolbar only while editing; for detail views).' },
        placeholder: { type: 'string', default: '', description: 'Shown in the empty editor.' },
        rows: { type: 'number', default: null, description: 'Least height in lines, like a textarea: 6 by default, 3 for inline.' },
        uploadUrl: { type: 'string', default: null, description: 'The host\'s picture endpoint: turns on the picture tool and image drop / paste.' },
        resolveUrl: { type: 'string', default: null, description: 'The host\'s link resolver: a pasted address of the page\'s own origin gets its title.' },
        uploadToken: {
            type: 'string',
            default: null,
            blade: false,
            description: 'The CSRF token sent with each upload as X-CSRF-TOKEN; defaults to the page\'s <meta name="csrf-token">.',
        },
    },
    parts: {
        toolbar: { description: 'The toolbar row (below the text in the inline variant).' },
        tool: { description: 'A square tool button (formatting, undo, redo, and the link row\'s buttons).' },
        separator: { description: 'The hairline between the mark tools and the block tools.' },
        end: { description: 'Undo, redo and the upload indicator, pushed to the right.' },
        uploading: { description: 'The spinning indicator while pictures upload (role="status").' },
        link: { description: 'The link row (also data-sigta="editor-link"): the address, then apply, open and remove.' },
        'link-input': { description: 'The link address field.' },
        mount: { description: 'Where TipTap mounts (the id the toolbar controls); its least height comes from rows (--sigta-editor-rows).' },
        content: { description: 'The editable element TipTap creates (class sigta-prose), filling the box so a click anywhere lands in the text.' },
        picker: { description: 'The hidden file input behind the picture tool.' },
    },
    states: {
        variant: { attribute: 'data-variant', values: ['default', 'inline'], description: 'On the root.' },
        format: { attribute: 'data-format', values: ['html', 'markdown'], description: 'On the root.' },
        invalid: { attribute: 'data-invalid', description: 'On the root, with an error.' },
        pressed: { attribute: 'aria-pressed', description: 'On a tool whose mark or block is at the caret (set once the editor runs).' },
    },
    events: {
        change: {
            description: 'The content changed (serialized in the format; empty when the editor is).',
            blade: 'input on the hidden input (wire:model hears it)',
            svelte: 'bind:value',
            detail: 'string',
        },
        set: {
            description: 'Replaces the content on purpose (restore a draft, switch records): dispatch it on the root with the new value as detail.',
            blade: 'sigta-editor-set',
            svelte: 'sigta-editor-set',
            detail: 'string',
        },
    },
    uses: ['field', 'icon'],
    usage: {
        livewire: `<sigta:editor name="body" wire:model="body" :value="$post->body" label="Body" />
<sigta:editor format="markdown" variant="inline" placeholder="Add a description" rows="3" wire:model="description" :value="$description" />
<sigta:editor format="markdown" wire:model="body" :value="$body" :upload-url="route('images.store')" :resolve-url="route('links.resolve')" />`,
        svelte: `<script>
    import { Editor } from '@sigta-hq/svelte';

    let body = $state('');
    let description = $state('');
</script>

<Editor name="body" label="Body" bind:value={body} />
<Editor format="markdown" variant="inline" placeholder="Add a description" rows={3} bind:value={description} />
<Editor format="markdown" bind:value={body} uploadUrl="/images" resolveUrl="/links/resolve" />`,
    },
    notes: {
        shared: [
            'Toolbar: bold, italic, strike, link, heading, bullet and numbered lists, quote, code block, plus a picture tool when an upload URL is set; undo and redo at the end.',
            'Links: Cmd/Ctrl+K or the toolbar edits (with Open and Remove), Cmd/Ctrl+click follows; a plain click only places the caret, so editing is never interrupted.',
            'format="markdown" parses the value as markdown and writes markdown (single newlines are line breaks); otherwise it is HTML.',
            'Pictures: with an upload URL, a dropped, pasted or picked image is posted there as multipart `file` with the CSRF token in X-CSRF-TOKEN; the endpoint answers JSON {url, name?} and the picture is inserted as an image node (`![name](url)` in markdown). A failed upload raises a warning toast and inserts nothing. Data URLs are refused.',
            'Titled links: with a resolve URL, a pasted address on the page\'s own origin is linked at once, then GET <resolve-url>?url=… is asked for JSON {title, url?}; the link text becomes the title (and the href the returned url) when it answers, and stays the address when it does not. With text selected, a pasted address links the selection as before.',
            'Both runtimes run the same TipTap editor (@sigta-hq/core/editor); the text has the prose look (.sigta-prose).',
        ],
        livewire: [
            'Requires @sigtaEditorScripts in the layout head (the editor is its own bundle, loaded only where it is used).',
            'name and wire:model (and every attribute except class and id) land on a hidden input that the editor writes on every change. It never reads that input back, so Livewire re-renders cannot disturb typing; replace the content on purpose by dispatching sigta-editor-set on the root with the new value as detail.',
            'Without an id, the id derives from name or wire:model (sigta-editor-<slug>), so re-renders keep the mounted editor; the editable region is wire:ignore.',
            'upload-url / resolve-url name the endpoints; uploads carry the session\'s CSRF token.',
        ],
        svelte: [
            'bind:value carries the content; a value set from outside replaces it, and sigta-editor-set on the root works as well.',
            'uploadUrl / resolveUrl name the endpoints; uploads send uploadToken, or the page\'s <meta name="csrf-token">, as X-CSRF-TOKEN.',
            'TipTap is created on mount, so the server render is the toolbar over an empty page. Other attributes (name, data-*, oninput) land on the hidden value input, which hears an input event on every change.',
        ],
    },
    examples: [
        {
            // HTML in a value is a PHP expression in Blade (a plain attribute would be escaped).
            label: 'Rich text (TipTap)',
            conformance: 'browser',
            blade: `<x-sigta::editor
    name="body"
    label="Project brief"
    description="Bold, italic, headings, lists, quotes, code, links. Saved as HTML."
    :value="'${brief}'"
/>`,
            svelte: `<script>
    import { Editor } from '@sigta-hq/svelte';

    let body = $state('${brief}');
</script>

<Editor name="body" label="Project brief" description="Bold, italic, headings, lists, quotes, code, links. Saved as HTML." bind:value={body} />`,
        },
        {
            label: 'Markdown (stored as markdown, edited visually)',
            conformance: 'browser',
            tree: [c('editor', {
                name: 'notes',
                format: 'markdown',
                label: 'Release notes',
                description: 'The hidden input carries markdown. Type # for a heading, - for a list, **bold**, or paste a URL. Cmd/Ctrl+K links the selection.',
                value: notes,
            })],
        },
        {
            label: 'Inline (always editable, toolbar while editing)',
            conformance: 'browser',
            tree: [c('editor', { name: 'description', format: 'markdown', variant: 'inline', label: 'Description', placeholder: 'Add a description', rows: 3 })],
        },
        {
            label: 'Pictures and titled links (host endpoints)',
            conformance: 'browser',
            tree: [c('editor', {
                name: 'article',
                format: 'markdown',
                label: 'Article',
                description: 'Drop or paste a picture, or use the picture tool: it is posted to upload-url and inserted as an image. Paste an address from this site and the resolver at resolve-url gives the link its title.',
                uploadUrl: '/kit-api/upload',
                resolveUrl: '/kit-api/resolve',
                value: 'Paste a link to any kit page, for example this one, and drop a picture below.',
            })],
        },
        {
            label: 'Empty + error state',
            conformance: 'browser',
            tree: [c('editor', { name: 'empty', label: 'Notes', error: 'Notes can\'t be empty.' })],
        },
        {
            label: 'Typing, then bold',
            conformance: 'browser',
            tree: [el('div', { class: 'max-w-xl' }, c('editor', { name: 'typed', label: 'Notes', placeholder: 'Write something' }))],
            interactions: [
                { click: content },
                { fill: [content, 'Written by hand'] },
                { press: ['Control+a'] },
                { click: '[data-part="tool"][aria-label="Bold"]' },
            ],
        },
        {
            label: 'Linking the selection',
            conformance: 'browser',
            tree: [el('div', { class: 'max-w-xl' }, c('editor', { name: 'linked', format: 'markdown', label: 'Notes', value: 'Read the docs' }))],
            interactions: [
                { click: content },
                { press: ['Control+a'] },
                { click: '[data-part="tool"][aria-label="Link"]' },
                { fill: ['[data-part="link-input"]', 'https://sigta.dk'] },
                { press: ['Enter'] },
            ],
        },
        {
            label: 'Inline, editing',
            conformance: 'browser',
            tree: [el('div', { class: 'max-w-xl px-4' }, c('editor', { name: 'summary', format: 'markdown', variant: 'inline', label: 'Summary', placeholder: 'Add a summary', rows: 2 }))],
            interactions: [{ click: content }, { press: ['Control+k'] }],
        },
    ],
});
