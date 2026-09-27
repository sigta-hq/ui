<div class="flex max-w-lg flex-col gap-4">
    <div class="flex items-center justify-between gap-4">
        <x-sigta::eyebrow>Autosaving note</x-sigta::eyebrow>
        <x-sigta::save-state target="title, body" idle="All changes saved" />
    </div>

    <x-sigta::input wire:model.live.debounce.500ms="title" aria-label="Title" />
    <x-sigta::textarea wire:model.live.debounce.700ms="body" rows="4" aria-label="Note" />
    @error('body')
        <x-sigta::text variant="caption" class="text-danger">{{ $message }}</x-sigta::text>
    @enderror

    <div class="flex items-center gap-4 border-t border-line pt-4">
        <x-sigta::button variant="primary" size="sm" wire:click="save">Save now</x-sigta::button>
        <x-sigta::save-state target="save" />
    </div>
</div>
