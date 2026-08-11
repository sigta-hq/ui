{{-- Command palette: ⌘K/Ctrl-K (or a <x-sigta::modal.trigger name="...">) opens a
     dialog with a filtering combobox of <x-sigta::command.item>s. --}}
@props([
    'name' => 'command',
    'placeholder' => 'Type a command…',
    'shortcut' => true,
])

<div
    x-data="sigtaModal(@js($name))"
    x-on:sigta-modal-show.window="onShow($event.detail)"
    x-on:sigta-modal-hide.window="onHide($event.detail)"
    @if ($shortcut)
        x-on:keydown.meta.k.window.prevent="show()"
        x-on:keydown.ctrl.k.window.prevent="show()"
    @endif
    data-sigta="command"
>
    <dialog
        x-ref="dialog"
        x-on:click="backdropClick($event)"
        {{ $attributes->merge(['class' => 'w-[calc(100vw-2.5rem)] max-w-xl bg-surface-raised text-ink border border-line rounded-frame shadow-paper p-0 backdrop:bg-ink/45 backdrop:backdrop-blur-[2px] m-auto mt-[12vh]']) }}
    >
        <div
            x-data="sigtaCombobox()"
            x-on:keydown.escape.prevent="hide()"
        >
            <div class="flex items-center gap-2.5 px-4 border-b border-line">
                <x-sigta::icon name="search" size="sm" class="text-graphite shrink-0" />
                <input
                    type="text"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-controls="sigta-command-list"
                    x-bind:aria-expanded="true"
                    x-bind:aria-activedescendant="activeId"
                    x-model="query"
                    x-on:input="filter()"
                    x-on:keydown.down.prevent="move(1)"
                    x-on:keydown.up.prevent="move(-1)"
                    x-on:keydown.enter.prevent="activeEl()?.click()"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    class="w-full font-mono text-[0.85rem] text-ink bg-transparent py-3.5 focus:outline-none placeholder:text-graphite"
                >
                <kbd class="font-mono text-[0.62rem] text-graphite border border-line rounded-base px-1.5 py-0.5">esc</kbd>
            </div>

            <div id="sigta-command-list" role="listbox" class="max-h-80 overflow-y-auto py-2">
                {{ $slot }}
                <div x-show="visible().length === 0" class="px-4 py-3 font-mono text-[0.74rem] text-graphite">Nothing found — try different ink.</div>
            </div>
        </div>
    </dialog>
</div>
