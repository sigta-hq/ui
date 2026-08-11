<x-kit-shell title="Editor">
    <x-slot:head>
        @sigtaEditorScripts
    </x-slot:head>

    <x-kit-example label="Rich text (TipTap)">
    «sigta:editor
        name="body"
        label="Project brief"
        description="Bold, italic, headings, lists, quotes, code. Saved as HTML."
        :value="'«h2>Written by hand«/h2>«p>We design and build software the «strong>careful«/strong> way — then keep the best of it as «em>products we run ourselves«/em>.«/p>«ul>«li>Researched«/li>«li>Drawn«/li>«li>Tested«/li>«/ul>'"
    />
    </x-kit-example>

    <x-kit-example label="Empty + error state">
    «sigta:editor name="empty" label="Notes" error="Notes can't be empty." />
    </x-kit-example>
</x-kit-shell>
