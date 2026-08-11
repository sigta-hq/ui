{{-- A representative component cluster, authored once and reused under both the
     default Sigta brand and [data-sigta-theme="studio"]. Identical markup, the
     surrounding theme decides the look — that is the whole point of the tokens. --}}
<div class="flex flex-col gap-4">
    <div class="flex flex-wrap items-center gap-2">
        <x-sigta::badge variant="solid">Live</x-sigta::badge>
        <x-sigta::badge>Draft</x-sigta::badge>
        <x-sigta::badge variant="accent">New</x-sigta::badge>
    </div>

    <x-sigta::card :hover="false">
        <x-sigta::heading level="3" class="mb-1">Marginalia v3</x-sigta::heading>
        <x-sigta::text variant="muted" class="text-[0.9rem]">Kickoff on Friday. Two seats still open on the workspace.</x-sigta::text>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <x-sigta::stat label="Active seats" value="14" delta="+3 this week" trend="up" />
            <x-sigta::stat label="Open tasks" value="27" delta="-5 today" trend="down" />
        </div>
    </x-sigta::card>

    <x-sigta::callout variant="success" heading="Deploy complete.">Plumb 2.4 is live for all workspaces.</x-sigta::callout>

    <x-sigta::input label="Invite email" type="email" icon="mail" placeholder="you@studio.co" />

    <div class="flex flex-wrap items-center gap-3">
        <x-sigta::button variant="cta">Book a project <x-sigta::icon name="arrow-right" size="sm" /></x-sigta::button>
        <x-sigta::button variant="ghost">Cancel</x-sigta::button>
    </div>
</div>
