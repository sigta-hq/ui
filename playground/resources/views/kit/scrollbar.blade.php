<x-kit-shell title="Scrollbar">
    <x-sigta::text variant="muted" class="max-w-[60ch] -mt-4">
        Every scroll area gets the Sigta scrollbar from the foundation, with no class to add. It is drawn in the
        colour of the text it scrolls, so a dark band carries a light one. Rest on an area to darken its bar.
    </x-sigta::text>

    <x-kit-example label="Scroll area">
    «div class="max-w-md max-h-52 overflow-y-auto rounded-panel border border-line bg-surface">
        «div class="divide-y divide-line">
            «p class="px-4 py-2.5">Draft the proposal for Nordlys«/p>
            «p class="px-4 py-2.5">Review the contract«/p>
            «p class="px-4 py-2.5">Send the invoice for October«/p>
            «p class="px-4 py-2.5">Book the kickoff call«/p>
            «p class="px-4 py-2.5">Sketch the onboarding flow«/p>
            «p class="px-4 py-2.5">Migrate the customer notes«/p>
            «p class="px-4 py-2.5">Write the release notes«/p>
            «p class="px-4 py-2.5">Plan the next sprint«/p>
            «p class="px-4 py-2.5">Archive last year's projects«/p>
            «p class="px-4 py-2.5">Answer the support inbox«/p>
        «/div>
    «/div>
    </x-kit-example>

    <x-kit-example label="Wide content">
    «div class="flex gap-3 overflow-x-auto pb-1">
        «sigta:panel class="w-44 shrink-0 p-4">To do«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Doing«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Review«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Waiting«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Done«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Archived«/sigta:panel>
    «/div>
    </x-kit-example>

    <x-kit-example label="On an inverse band">
    «sigta:panel variant="inverse" class="block max-w-md max-h-40 overflow-y-auto p-4">
        «p class="font-mono text-[0.78rem] leading-relaxed">composer require sigta/ui«/p>
        «p class="font-mono text-[0.78rem] leading-relaxed">php artisan sigta:install«/p>
        «p class="font-mono text-[0.78rem] leading-relaxed">npm install«/p>
        «p class="font-mono text-[0.78rem] leading-relaxed">npm run build«/p>
        «p class="font-mono text-[0.78rem] leading-relaxed">php artisan migrate«/p>
        «p class="font-mono text-[0.78rem] leading-relaxed">php artisan serve«/p>
        «p class="font-mono text-[0.78rem] leading-relaxed">php artisan test«/p>
        «p class="font-mono text-[0.78rem] leading-relaxed">git push«/p>
    «/sigta:panel>
    </x-kit-example>

    <x-kit-example label="Hidden (a strip with its own controls)">
    «div class="flex gap-3 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        «sigta:panel class="w-44 shrink-0 p-4">One«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Two«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Three«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Four«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Five«/sigta:panel>
        «sigta:panel class="w-44 shrink-0 p-4">Six«/sigta:panel>
    «/div>
    </x-kit-example>
</x-kit-shell>
