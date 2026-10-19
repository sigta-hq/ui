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
     Mentions: pass mention-url and typing @ asks GET mention-url?q=… (the words
     after the @) for a JSON list of [{label, id?, url?, group?, description?}],
     shown beside the caret. An answer with a url is inserted as a link titled
     with its label; one with an id becomes a mention node (`<span data-type=
     "mention" data-id>` in HTML, `[@ id="…" label="…"]` in markdown), so the
     host reads who was named by id. Consecutive answers sharing a group are
     listed under it.
     Usage:
       <x-sigta::editor name="body" wire:model="body" :value="$post->body" label="Body" />
       <x-sigta::editor format="markdown" variant="inline" placeholder="Add a description"
           rows="3" wire:model="description" :value="$description" />
       <x-sigta::editor format="markdown" wire:model="body" :value="$body"
           :upload-url="route('images.store')" :resolve-url="route('links.resolve')"
           :mention-url="route('mentions.index')" /> --}}
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
    'mentionUrl' => null,
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

$btn = 'flex size-8 items-center justify-center rounded-base text-ink cursor-pointer transition-colors duration-150 hover:bg-ink/5';

$frame = match (true) {
    $inline => 'flex flex-col rounded-[5px] border-[1.5px] transition-colors -mx-[0.8em] hover:border-line focus-within:border-ink '.($error ? 'border-danger' : 'border-transparent'),
    default => 'flex flex-col bg-surface border-[1.5px] rounded-[5px] overflow-hidden focus-within:border-ink transition-colors '.($error ? 'border-danger' : 'border-line'),
};

$toolbarFrame = $inline
    ? 'order-last border-t border-line px-1.5 py-1'
    : 'border-b border-line px-1.5 py-1';

// The padding goes on the editable element itself (the runtime applies these
// classes to it), so the whole box is editable surface: a click anywhere in it
// places the caret, instead of only the line the text happens to occupy.
$contentClass = $inline
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
        @if ($uploadUrl) data-upload-url="{{ $uploadUrl }}" data-upload-token="{{ csrf_token() }}" @endif
        @if ($resolveUrl) data-resolve-url="{{ $resolveUrl }}" @endif
        @if ($mentionUrl) data-mention-url="{{ $mentionUrl }}" @endif
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
                @if ($uploadUrl)
                    <span x-show="uploading > 0" x-cloak class="flex size-8 items-center justify-center text-graphite" role="status" aria-label="Uploading"><x-sigta::icon name="loader-circle" size="sm" class="animate-spin" /></span>
                @endif
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
        <div id="{{ $id }}" x-ref="element" wire:ignore class="flex cursor-text flex-col" data-content-class="{{ $contentClass }}" style="{{ $minHeight }}"></div>

        @if ($mentionUrl)
            {{-- What the @ before the caret could name. The runtime floats it beside the caret
                 and drives it from the keyboard while the caret stays in the text. --}}
            <div
                x-ref="mentionPanel"
                x-show="mentionItems.length > 0"
                x-cloak
                id="{{ $id }}-mentions"
                role="listbox"
                aria-label="Mention"
                class="z-50 max-h-72 min-w-56 max-w-80 overflow-y-auto rounded-panel border border-line bg-surface-raised py-1.5 shadow-paper"
                data-sigta="editor-mentions"
            >
                <template x-for="(item, index) in mentionItems" x-bind:key="index">
                    <div>
                        <div
                            x-show="item.group && (index === 0 || mentionItems[index - 1].group !== item.group)"
                            x-text="item.group"
                            class="px-3.5 pb-1 pt-2 font-mono text-[0.62rem] uppercase tracking-[0.14em] text-graphite"
                        ></div>
                        <div
                            role="option"
                            x-bind:id="'{{ $id }}-mention-' + index"
                            x-bind:aria-selected="index === mentionActive ? 'true' : 'false'"
                            x-bind:data-active="index === mentionActive ? 'true' : null"
                            {{-- mousedown is prevented so the editor keeps its caret while an answer is clicked. --}}
                            x-on:mousedown.prevent
                            x-on:mousemove="mentionActive = index"
                            x-on:click="chooseMention(index)"
                            class="flex cursor-pointer items-baseline justify-between gap-4 px-3.5 py-1.5 text-ink data-active:bg-ink data-active:text-surface"
                        >
                            <span class="truncate font-body text-[0.95rem]" x-text="item.label"></span>
                            <span x-show="item.description" x-text="item.description" class="max-w-[45%] shrink-0 truncate font-mono text-[0.66rem] uppercase tracking-[0.06em] opacity-70"></span>
                        </div>
                    </div>
                </template>
            </div>
        @endif

        @if ($uploadUrl)
            {{-- The picker behind the toolbar's picture tool; drop and paste reach the runtime directly. --}}
            <input type="file" x-ref="imageInput" accept="image/*" multiple class="hidden" tabindex="-1" aria-hidden="true" x-on:change="pickedImages($event)">
        @endif

        <input type="hidden" x-ref="value" @if ($name) name="{{ $name }}" @endif value="{{ $value }}" {{ $attributes->except(['class', 'id']) }}>
    </div>
</x-sigta::field>
