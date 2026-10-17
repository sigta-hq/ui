{{-- Dropzone file input. The zone is a <label> wrapping a visually hidden
     native <input type="file"> — clicking opens the picker, and every attribute
     (name, accept, wire:model, ...) forwards onto the native input, so
     Livewire uploads work untouched. Dropped files are assigned to the input
     and a `change` event is re-dispatched. The behavior is sigtaFileUpload in
     resources/js/sigta.js. --}}
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

$controlAttributes = ['id' => $id];

if ($error) {
    $controlAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $controlAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div data-sigta="file-upload" x-data="sigtaFileUpload">
        <label
            data-scope="file-upload"
            data-part="zone"
            @if ($error) data-invalid @endif
            @if ($disabled) data-disabled @endif
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
                data-scope="file-upload"
                data-part="input"
                {{ $attributes->merge($controlAttributes) }}
            />

            <x-sigta::icon name="upload-cloud" size="lg" data-scope="file-upload" data-part="icon" />

            <span data-scope="file-upload" data-part="helper">
                {{ $helper }}
                <span data-scope="file-upload" data-part="browse">browse</span>
            </span>
        </label>

        <ul x-show="files.length" x-cloak data-scope="file-upload" data-part="list">
            <template x-for="(file, index) in files" :key="index">
                <li data-scope="file-upload" data-part="file">
                    <span x-text="file.name" data-scope="file-upload" data-part="name"></span>
                    <span x-text="file.size" data-scope="file-upload" data-part="size"></span>
                </li>
            </template>
        </ul>
    </div>
</x-sigta::field>
