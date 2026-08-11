{{-- Rich text editor (TipTap). Requires @sigtaEditorScripts in the layout head.
     The hidden input carries name / wire:model; TipTap writes HTML into it on
     every update. Toolbar buttons reflect active marks via the sigtaEditor
     Alpine data.
     Usage:
       <x-sigta::editor name="body" wire:model="body" :value="$post->body" label="Body" /> --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'name' => null,
    'value' => '',
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$tools = [
    ['bold', 'bold', 'Bold'],
    ['italic', 'italic', 'Italic'],
    ['strike', 'strikethrough', 'Strikethrough'],
    ['__sep'],
    ['heading', 'heading-2', 'Heading', ['level' => 2]],
    ['bulletList', 'list', 'Bullet list'],
    ['orderedList', 'list-ordered', 'Numbered list'],
    ['blockquote', 'quote', 'Quote'],
    ['codeBlock', 'code', 'Code block'],
];

$btn = 'flex size-8 items-center justify-center rounded-base text-ink cursor-pointer transition-colors duration-150 hover:bg-ink/5';
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="editor"
        x-data="sigtaEditor(@js($value))"
        {{ $attributes->only('class')->merge(['class' => 'bg-surface border-[1.5px] rounded-[5px] overflow-hidden focus-within:border-ink transition-colors '.($error ? 'border-danger' : 'border-line')]) }}
    >
        <div role="toolbar" aria-label="Formatting" aria-controls="{{ $id }}" class="flex flex-wrap items-center gap-0.5 border-b border-line px-1.5 py-1">
            @foreach ($tools as $tool)
                @if ($tool[0] === '__sep')
                    <span class="mx-1 h-5 w-px bg-line" aria-hidden="true"></span>
                @else
                    <button
                        type="button"
                        class="{{ $btn }}"
                        aria-label="{{ $tool[2] }}"
                        x-on:click="run('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }})"
                        x-bind:aria-pressed="isActive('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }})"
                        x-bind:class="isActive('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }}) ? 'bg-ink text-surface hover:bg-ink' : ''"
                    >
                        <x-sigta::icon :name="$tool[1]" size="sm" />
                    </button>
                @endif
            @endforeach

            <span class="ml-auto flex items-center gap-0.5">
                <button type="button" class="{{ $btn }}" aria-label="Undo" x-on:click="run('undo')"><x-sigta::icon name="undo-2" size="sm" /></button>
                <button type="button" class="{{ $btn }}" aria-label="Redo" x-on:click="run('redo')"><x-sigta::icon name="redo-2" size="sm" /></button>
            </span>
        </div>

        {{-- TipTap mounts here; min height gives an inviting editing area. --}}
        <div id="{{ $id }}" x-ref="element" class="px-4 py-3 min-h-40 max-w-none"></div>

        <input type="hidden" x-ref="value" @if ($name) name="{{ $name }}" @endif value="{{ $value }}" {{ $attributes->except(['class', 'id']) }}>
    </div>
</x-sigta::field>
