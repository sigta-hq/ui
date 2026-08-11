{{-- Dropzone file input. The zone is a <label> wrapping an sr-only native
     <input type="file"> — clicking opens the picker, and every attribute
     (name, accept, wire:model, ...) forwards onto the native input, so
     Livewire uploads work untouched. Dropped files are assigned to the input
     and a `change` event is re-dispatched. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'multiple' => false,
    'helper' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$disabled = $attributes->has('disabled');

$helper ??= $multiple ? 'Drag files here, or' : 'Drag a file here, or';

$zone = 'flex w-full cursor-pointer flex-col items-center gap-3 rounded-panel border-[1.5px] border-dashed px-6 py-9 text-center transition duration-200 ease-out hover:border-ink hover:bg-ink/5 data-[dragging]:border-ink data-[dragging]:bg-ink/5 has-[:focus-visible]:outline-[2.5px] has-[:focus-visible]:outline-ink has-[:focus-visible]:outline-offset-[3px]';
$zone .= $disabled ? ' opacity-40 pointer-events-none' : '';
$zone .= $error ? ' border-danger' : ' border-line';

$controlAttributes = ['id' => $id, 'class' => 'sr-only'];

if ($error) {
    $controlAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $controlAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="file-upload"
        class="flex w-full flex-col gap-2"
        x-data="{
            dragging: 0,
            files: [],
            update(input) {
                this.files = Array.from(input.files || []).map((file) => ({ name: file.name, size: this.format(file.size) }));
            },
            format(bytes) {
                const units = ['B', 'KB', 'MB', 'GB', 'TB'];
                let n = bytes, i = 0;
                while (n >= 1024 && i < units.length - 1) { n /= 1024; i++; }
                return (i === 0 ? n : n.toFixed(n >= 10 ? 0 : 1)) + ' ' + units[i];
            },
            drop(event) {
                this.dragging = 0;
                if (! event.dataTransfer?.files?.length) return;
                this.$refs.input.files = event.dataTransfer.files;
                this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
            },
        }"
    >
        <label
            class="{{ $zone }}"
            :data-dragging="dragging > 0 || null"
            @dragenter.prevent="dragging++"
            @dragleave.prevent="dragging = Math.max(0, dragging - 1)"
            @dragover.prevent
            @drop.prevent="drop($event)"
        >
            <input
                type="file"
                x-ref="input"
                @change="update($event.target)"
                @if ($multiple) multiple @endif
                {{ $attributes->merge($controlAttributes) }}
            />

            <x-sigta::icon name="upload-cloud" size="lg" class="text-graphite" />

            <span class="font-mono text-[0.72rem] tracking-[0.04em] text-graphite">
                {{ $helper }}
                <span class="ml-1 inline-flex items-center rounded-base border-[1.5px] border-ink px-[1em] py-[0.5em] text-ink transition duration-150 ease-out">browse</span>
            </span>
        </label>

        <ul x-show="files.length" x-cloak class="m-0 flex list-none flex-col gap-1 p-0 font-mono text-[0.72rem]">
            <template x-for="(file, index) in files" :key="index">
                <li class="flex items-baseline justify-between gap-4 border-b border-line pb-1 last:border-b-0">
                    <span class="truncate text-ink" x-text="file.name"></span>
                    <span class="shrink-0 text-graphite tabular-nums" x-text="file.size"></span>
                </li>
            </template>
        </ul>
    </div>
</x-sigta::field>
