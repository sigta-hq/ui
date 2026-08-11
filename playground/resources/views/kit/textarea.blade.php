<x-kit-shell title="Textarea">
    <x-kit-example label="Basic">
    «div class="max-w-sm">
        «sigta:textarea label="Project brief" placeholder="What are we drawing?" />
    «/div>
    </x-kit-example>

    <x-kit-example label="With description">
    «div class="max-w-sm">
        «sigta:textarea label="Notes" description="Visible to the whole team." rows="3">Kickoff moved to Tuesday.«/sigta:textarea>
    «/div>
    </x-kit-example>

    <x-kit-example label="Error">
    «div class="max-w-sm">
        «sigta:textarea label="Project brief" error="Give us at least a sentence." />
    «/div>
    </x-kit-example>

    <x-kit-example label="Taller (rows=8) & disabled">
    «div class="flex max-w-sm flex-col gap-4">
        «sigta:textarea label="Scope" rows="8" placeholder="Everything we agreed to build…" />
        «sigta:textarea label="Archived brief" disabled>Locked after sign-off.«/sigta:textarea>
    «/div>
    </x-kit-example>
</x-kit-shell>
