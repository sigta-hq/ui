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
     Pictures: pass upload-url and a dropped, pasted or picked image is posted
     there as multipart `file` with the session's CSRF token; the endpoint answers
     JSON {url, name?} and the picture lands in the text as an image node (markdown
     `![name](url)`). Without upload-url the toolbar offers no picture tool and
     drops fall through. Links: pass resolve-url and a pasted address on the page's
     own origin is linked at once, then the resolver is asked GET resolve-url?url=…
     for JSON {title, url?}; the link's text becomes the title when it answers,
     and stays the address when it does not.
     The editor itself (extensions, markdown, commands, links, uploads) is
     @sigta-hq/core's editor.ts, shared with the Svelte Editor; resources/js/editor.js
     is its Alpine glue. The look is the contract's editor.css (+ prose.css).
     Usage:
       <x-sigta::editor name="body" wire:model="body" :value="$post->body" label="Body" />
       <x-sigta::editor format="markdown" variant="inline" placeholder="Add a description"
           rows="3" wire:model="description" :value="$description" />
       <x-sigta::editor format="markdown" wire:model="body" :value="$body"
           :upload-url="route('images.store')" :resolve-url="route('links.resolve')" /> --}}
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
    'uploadUrl' => null,
    'resolveUrl' => null,
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

if ($uploadUrl) {
    $tools[] = ['image', 'image', 'Picture'];
}

// Like a textarea's rows: the empty editor is an inviting area, not a single
// line. Inline fields default to three lines, the form variant to six; the
// contract turns the number into a min-height in em of the prose line height
// plus the vertical padding, so it tracks the font.
$rows = (int) ($rows ?? ($inline ? 3 : 6));
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="editor"
        data-format="{{ $format }}"
        data-variant="{{ $variant }}"
        data-placeholder="{{ $placeholder }}"
        @if ($uploadUrl) data-upload-url="{{ $uploadUrl }}" data-upload-token="{{ csrf_token() }}" @endif
        @if ($resolveUrl) data-resolve-url="{{ $resolveUrl }}" @endif
        {{-- The runtime reads its initial content and options from the DOM. Interpolating
             them into x-data would change that attribute on every Livewire morph, and
             Alpine re-runs x-data when it changes, which would rebuild the editor mid-typing. --}}
        x-data="sigtaEditor()"
        x-on:sigta-editor-set="setContent($event.detail)"
        x-on:keydown.meta.k.prevent="openLink()"
        x-on:keydown.ctrl.k.prevent="openLink()"
        x-on:click="followLink($event)"
        @if ($error) data-invalid @endif
        {{ $attributes->only('class') }}
    >
        <div
            role="toolbar"
            aria-label="Formatting"
            aria-controls="{{ $id }}"
            data-scope="editor" data-part="toolbar"
            @if ($inline) x-show="focused || linkOpen" x-cloak @endif
        >
            @foreach ($tools as $tool)
                @if ($tool[0] === '__sep')
                    <span data-scope="editor" data-part="separator" aria-hidden="true"></span>
                @else
                    {{-- mousedown is prevented so the editor keeps focus and selection while a tool is clicked. --}}
                    <button
                        type="button"
                        data-scope="editor" data-part="tool"
                        aria-label="{{ $tool[2] }}"
                        x-on:mousedown.prevent
                        x-on:click="run('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }})"
                        x-bind:aria-pressed="isActive('{{ $tool[0] }}'{{ isset($tool[3]) ? ', '.\Illuminate\Support\Js::from($tool[3]) : '' }})"
                    >
                        <x-sigta::icon :name="$tool[1]" size="sm" />
                    </button>
                @endif
            @endforeach

            <span data-scope="editor" data-part="end">
                @if ($uploadUrl)
                    <span x-show="uploading > 0" x-cloak data-scope="editor" data-part="uploading" role="status" aria-label="Uploading"><x-sigta::icon name="loader-circle" size="sm" /></span>
                @endif
                <button type="button" data-scope="editor" data-part="tool" aria-label="Undo" x-on:mousedown.prevent x-on:click="run('undo')"><x-sigta::icon name="undo-2" size="sm" /></button>
                <button type="button" data-scope="editor" data-part="tool" aria-label="Redo" x-on:mousedown.prevent x-on:click="run('redo')"><x-sigta::icon name="redo-2" size="sm" /></button>
            </span>

            <div x-show="linkOpen" x-cloak data-sigta="editor-link" data-scope="editor" data-part="link">
                <input
                    x-ref="linkInput"
                    x-model="linkUrl"
                    type="url"
                    inputmode="url"
                    placeholder="https://"
                    aria-label="Link address"
                    data-scope="editor" data-part="link-input"
                    x-on:keydown.enter.prevent.stop="applyLink()"
                    x-on:keydown.escape.prevent.stop="closeLink()"
                />
                <button type="button" data-scope="editor" data-part="tool" aria-label="Apply link" x-on:mousedown.prevent x-on:click="applyLink()"><x-sigta::icon name="check" size="sm" /></button>
                <button type="button" data-scope="editor" data-part="tool" aria-label="Open link" x-on:mousedown.prevent x-on:click="visitLink()" x-bind:disabled="linkUrl.trim() === ''"><x-sigta::icon name="external-link" size="sm" /></button>
                <button type="button" data-scope="editor" data-part="tool" aria-label="Remove link" x-on:mousedown.prevent x-on:click="removeLink()"><x-sigta::icon name="unlink" size="sm" /></button>
            </div>
        </div>

        {{-- TipTap mounts here and owns this subtree, so Livewire must never morph it.
             The editable element it creates is the content part (its padding is the
             contract's), filling the box so a click anywhere in it lands in the text. --}}
        <div id="{{ $id }}" x-ref="element" wire:ignore data-scope="editor" data-part="mount" style="--sigta-editor-rows: {{ $rows }}"></div>

        @if ($uploadUrl)
            {{-- The picker behind the toolbar's picture tool; drop and paste reach the runtime directly. --}}
            <input type="file" x-ref="imageInput" accept="image/*" multiple tabindex="-1" aria-hidden="true" data-scope="editor" data-part="picker" x-on:change="pickedImages($event)">
        @endif

        <input type="hidden" x-ref="value" @if ($name) name="{{ $name }}" @endif value="{{ $value }}" {{ $attributes->except(['class', 'id']) }}>
    </div>
</x-sigta::field>
