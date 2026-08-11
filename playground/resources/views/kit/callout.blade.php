<x-kit-shell title="Callout">
    <x-kit-example label="Variants">
    «div class="flex flex-col gap-4">
        «sigta:callout heading="Heads up.">Two seats are unassigned. Add teammates before the trial ends Friday.«/sigta:callout>
        «sigta:callout variant="success" heading="Deploy complete.">Plumb 2.4 is live for all workspaces — no migration needed.«/sigta:callout>
        «sigta:callout variant="danger" heading="Payment failed.">We couldn't charge the card on file. Update billing to keep the workspace open.«/sigta:callout>
    «/div>
    </x-kit-example>

    <x-kit-example label="Without heading & with icon override">
    «div class="flex flex-col gap-4">
        «sigta:callout>Exports run nightly at 02:00 UTC. Yesterday's ledger is already in your inbox.«/sigta:callout>
        «sigta:callout icon="info" heading="For the record.">The info glyph, when a triangle feels too loud.«/sigta:callout>
    «/div>
    </x-kit-example>
</x-kit-shell>
