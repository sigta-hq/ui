{{-- Rich text editor (TipTap). Requires @sigtaEditorScripts in the layout head.
     The hidden input carries name / wire:model; TipTap writes HTML (or markdown,
     with format="markdown") into it on every update and never reads it back, so
     a host re-render can rewrite the input without disturbing what is being
     typed. To replace the content on purpose (restore a draft, switch records)
     dispatch `sigta-editor-set` on the root with the new value as detail.
     Links: Cmd/Ctrl+K (or the toolbar) edits, Cmd/Ctrl+click or the Open button
     follows; a plain click only places the caret, so editing is never interrupted.
     variant="inline" is the always-editable, borderless-until-hover form for
     fields that live in a detail view rather than a form; its toolbar shows
     only while editing.
     Usage:
       <x-sigta::editor name="body" wire:model="body" :value="$post->body" label="Body" />
       <x-sigta::editor format="markdown" variant="inline" placeholder="Add a description"
           rows="3" wire:model="description" :value="$description" /> --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'name' => null,
    'value' => '',
    'format' => 'html',
    'variant' => 'default',
    'placeholder' => '',
    'rows' => null,
])

@php
// A Livewire host re-renders on every save and keys the morph on ids, so a
// random id would swap the mounted editor for an empty one; derive a stable one
// from what the editor is bound to whenever no id is given.
$binding = $name ?? collect($attributes->getAttributes())->first(fn ($value, $key) => str_starts_with($key, 'wire:model'));
$id = $attributes->get('id') ?? (is_string($binding) && $binding !== '' ? 'sigta-editor-'.\Illuminate\Support\Str::slug($binding) : 'sigta-'.\Illuminate\Support\Str::random(8));
$format = $format === 'markdown' ? 'markdown' : 'html';
$inline = $variant === 'inline';

$tools = [
    ['bold', 'bold', 'Bold'],
    ['italic', 'italic', 'Italic'],
    ['strike', 'strikethrough', 'Strikethrough'],
    ['link', 'link', 'Link'],
    ['__sep'],
    ['heading', 'heading-2', 'Heading', ['level' => 2]],
    ['bulletList', 'list', 'Bullet list'],
    ['orderedList', 'list-ordered', 'Numbered list'],
    ['blockquote', 'quote', 'Quote'],
    ['codeBlock', 'code', 'Code block'],
];

$btn = 'flex size-8 items-center justify-center rounded-base text-ink cursor-pointer transition-colors duration-150 hover:bg-ink/5';

$frame = match (true) {
    $inline => 'flex flex-col rounded-[5px] border-[1.5px] transition-colors -mx-[0.8em] hover:border-line focus-within:border-ink '.($error ? 'border-danger' : 'border-transparent'),
    default => 'flex flex-col bg-surface border-[1.5px] rounded-[5px] overflow-hidden focus-within:border-ink transition-colors '.($error ? 'border-danger' : 'border-line'),
};

$toolbarFrame = $inline
    ? 'order-last border-t border-line px-1.5 py-1'
    : 'border-b border-line px-1.5 py-1';

$content = $inline
    ? 'px-[0.8em] py-[0.62em] max-w-none'
    : 'px-4 py-3 max-w-none';

// Like a textarea's rows: the empty editor is an inviting area, not a single
// line. Inline fields default to three lines, the form variant to six. Expressed
// in em of the prose line height plus the vertical padding, so it tracks the font.
$rows = (int) ($rows ?? ($inline ? 3 : 6));
$minHeight = 'min-height: calc('.$rows.' * 1.65em + '.($inline ? '1.24em' : '1.5em').');';
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="editor"
        data-format="{{ $format }}"
        data-variant="{{ $variant }}"
        data-placeholder="{{ $placeholder }}"
        {{-- The runtime reads its initial content and options from the DOM. Interpolating
             them into x-data would change that attribute on every Livewire morph, and
             Alpine re-runs x-data when it changes, which would rebuild the editor mid-typing. --}}
        x-data="sigtaEditor()"
        x-on:sigta-editor-set="setContent($event.detail)"
        x-on:keydown.meta.k.prevent="openLink()"
        x-on:keydown.ctrl.k.prevent="openLink()"
        x-on:click="followLink($event)"
        {{ $attributes->only('class')->merge(['class' => $frame]) }}
    >
        <div
            role="toolbar"
            aria-label="Formatting"
            aria-controls="{{ $id }}"
            class="flex flex-wrap items-center gap-0.5 {{ $toolbarFrame }}"
            @if ($inline) x-show="focused || linkOpen" x-cloak @endif
        >
            @foreach ($tools as $tool)
                @if ($tool[0] === '__sep')
                    <span class="mx-1 h-5 w-px bg-line" aria-hidden="true"></span>
                @else
                    {{-- mousedown is prevented so the editor keeps focus and selection while a tool is clicked. --}}
                    <button
                        type="button"
                        class="{{ $btn }}"
                        aria-label="{{ $tool[2] }}"
                        x-on:mousedown.prevent
                        x-on:click="run('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }})"
                        x-bind:aria-pressed="isActive('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }})"
                        x-bind:class="isActive('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }}) ? 'bg-ink text-surface hover:bg-ink' : ''"
                    >
                        <x-sigta::icon :name="$tool[1]" size="sm" />
                    </button>
                @endif
            @endforeach

            <span class="ml-auto flex items-center gap-0.5">
                <button type="button" class="{{ $btn }}" aria-label="Undo" x-on:mousedown.prevent x-on:click="run('undo')"><x-sigta::icon name="undo-2" size="sm" /></button>
                <button type="button" class="{{ $btn }}" aria-label="Redo" x-on:mousedown.prevent x-on:click="run('redo')"><x-sigta::icon name="redo-2" size="sm" /></button>
            </span>

            <div x-show="linkOpen" x-cloak class="flex w-full items-center gap-1.5 py-1" data-sigta="editor-link">
                <input
                    x-ref="linkInput"
                    x-model="linkUrl"
                    type="url"
                    inputmode="url"
                    placeholder="https://"
                    aria-label="Link address"
                    class="min-w-0 flex-1 rounded-base border-[1.5px] border-line bg-surface px-2 py-1 font-mono text-[0.78rem] text-ink placeholder:text-graphite/70 focus:border-ink focus:outline-none"
                    x-on:keydown.enter.prevent.stop="applyLink()"
                    x-on:keydown.escape.prevent.stop="closeLink()"
                />
                <button type="button" class="{{ $btn }}" aria-label="Apply link" x-on:mousedown.prevent x-on:click="applyLink()"><x-sigta::icon name="check" size="sm" /></button>
                <button type="button" class="{{ $btn }}" aria-label="Open link" x-on:mousedown.prevent x-on:click="visitLink()" x-bind:disabled="linkUrl.trim() === ''"><x-sigta::icon name="external-link" size="sm" /></button>
                <button type="button" class="{{ $btn }}" aria-label="Remove link" x-on:mousedown.prevent x-on:click="removeLink()"><x-sigta::icon name="unlink" size="sm" /></button>
            </div>
        </div>

        {{-- TipTap mounts here and owns this subtree, so Livewire must never morph it. --}}
        <div id="{{ $id }}" x-ref="element" wire:ignore class="{{ $content }}" style="{{ $minHeight }}"></div>

        <input type="hidden" x-ref="value" @if ($name) name="{{ $name }}" @endif value="{{ $value }}" {{ $attributes->except(['class', 'id']) }}>
    </div>
</x-sigta::field>
