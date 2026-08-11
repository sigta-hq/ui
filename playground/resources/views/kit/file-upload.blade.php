<x-kit-shell title="File upload">
    <x-kit-example label="Single file">
    «div class="max-w-md">«sigta:file-upload label="Attachment" description="PDF or PNG, up to 10 MB." accept=".pdf,.png" />«/div>
    </x-kit-example>

    <x-kit-example label="Multiple">
    «div class="max-w-md">«sigta:file-upload label="Documents" multiple />«/div>
    </x-kit-example>

    <x-kit-example label="States">
    «div class="max-w-md flex flex-col gap-6">
        «sigta:file-upload label="Disabled" disabled />
        «sigta:file-upload label="Error" error="File too large." />
    «/div>
    </x-kit-example>
</x-kit-shell>
