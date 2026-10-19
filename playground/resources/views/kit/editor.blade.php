<x-kit-shell title="Editor">
    <x-slot:head>
        @sigtaEditorScripts
    </x-slot:head>

    <x-kit-example label="Rich text (TipTap)">
    «sigta:editor
        name="body"
        label="Project brief"
        description="Bold, italic, headings, lists, quotes, code, links. Saved as HTML."
        :value="'«h2>Written by hand«/h2>«p>We design and build software the «strong>careful«/strong> way, then keep the best of it as «em>products we run ourselves«/em>.«/p>«ul>«li>Researched«/li>«li>Drawn«/li>«li>Tested«/li>«/ul>'"
    />
    </x-kit-example>

    <x-kit-example label="Markdown (stored as markdown, edited visually)">
    «sigta:editor
        name="notes"
        format="markdown"
        label="Release notes"
        description="The hidden input carries markdown. Type # for a heading, - for a list, **bold**, or paste a URL. Cmd/Ctrl+K links the selection."
        :value="'## What changed' . PHP_EOL . PHP_EOL . '- Autosave for **every** field' . PHP_EOL . '- Links such as [the docs](https://sigta.dk)' . PHP_EOL . PHP_EOL . 'Line one' . PHP_EOL . 'Line two keeps its break.'"
    />
    </x-kit-example>

    <x-kit-example label="Inline (always editable, toolbar while editing)">
    «sigta:editor
        name="description"
        format="markdown"
        variant="inline"
        label="Description"
        placeholder="Add a description"
        rows="3"
    />
    </x-kit-example>

    <x-kit-example label="Pictures and titled links (host endpoints)">
    «sigta:editor
        name="article"
        format="markdown"
        label="Article"
        description="Drop or paste a picture, or use the picture tool: it is posted to upload-url and inserted as an image. Paste an address from this site and the resolver at resolve-url gives the link its title."
        upload-url="/kit-api/upload"
        resolve-url="/kit-api/resolve"
        :value="'Paste a link to any kit page, for example this one, and drop a picture below.' . PHP_EOL . PHP_EOL . '![Sigta](/kit-api/sample-image)'"
    />
    </x-kit-example>

    <x-kit-example label="Mentions (host endpoint)">
    «sigta:editor
        name="comment"
        format="markdown"
        label="Comment"
        description="Type @ and a few letters: mention-url answers with people and pages. A person becomes a mention, a page a link titled with its name."
        mention-url="/kit-api/mentions"
        rows="3"
        :value="'Thanks [@ id=\'4\' label=\'Søren Frisk\'], the notes are on the [Editor](/kit/editor) page.'"
    />
    </x-kit-example>

    <x-kit-example label="Empty + error state">
    «sigta:editor name="empty" label="Notes" error="Notes can't be empty." />
    </x-kit-example>
</x-kit-shell>
