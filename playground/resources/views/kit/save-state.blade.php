<x-kit-shell title="Save state">
    <x-sigta::text variant="muted" class="max-w-[60ch]">The compass stands in for "Saving…". It draws while a save is in flight, lands with a handwritten "Saved" as the mark completes, and shakes red when a save fails, validation errors included. Typing into a watched field closes the compass: unsaved.</x-sigta::text>

    <x-kit-example label="States">
    «div class="flex flex-wrap items-center gap-x-10 gap-y-4">
        «sigta:save-state state="dirty" />
        «sigta:save-state state="saving" />
        «sigta:save-state state="saved" />
        «sigta:save-state state="failed" />
        «sigta:save-state idle="All changes saved" />
    «/div>
    </x-kit-example>

    <x-kit-demo label="Livewire — follows the component by itself (target names actions and/or properties)">
        <livewire:kit.autosave />
        <pre class="mt-6 overflow-x-auto rounded-panel bg-inverse p-4 font-mono text-[0.76rem] leading-relaxed text-inverse-fg"><code>&lt;sigta:save-state target="title, body" idle="All changes saved" /&gt;
&lt;sigta:input wire:model.live.debounce.500ms="title" /&gt;

&lt;sigta:button wire:click="save"&gt;Save now&lt;/sigta:button&gt;
&lt;sigta:save-state target="save" /&gt;</code></pre>
    </x-kit-demo>

    <x-kit-example label="Alpine / JS — drive it by name with $sigta.save(promise, name)">
    «div class="flex max-w-lg flex-col gap-3" x-data="{ fail: false, draft() { return new Promise((resolve, reject) => setTimeout(() => this.fail ? reject(new Error('Demo failure')) : resolve(), 1200)) } }">
        «div class="flex items-center justify-between gap-4">
            «sigta:eyebrow>Profile«/sigta:eyebrow>
            «sigta:save-state name="profile" idle="Up to date" />
        «/div>
        «sigta:textarea rows="3" x-on:input="$sigta.saveState('dirty', 'profile')" x-on:input.debounce.700ms="$sigta.save(draft(), 'profile').catch(() => {})">A studio for paper-quiet software.«/sigta:textarea>
        «sigta:checkbox label="Make the next save fail" x-model="fail" />
    «/div>
    </x-kit-example>
</x-kit-shell>
