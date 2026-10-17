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
        {{ $attributes->merge(['data-scope' => 'command', 'data-part' => 'dialog']) }}
    >
        <div
            x-data="sigtaCombobox()"
            x-on:keydown.escape.prevent="hide()"
        >
            <div data-scope="command" data-part="search">
                <x-sigta::icon name="search" size="sm" data-scope="command" data-part="icon" />
                <input
                    type="text"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-controls="sigta-command-list"
                    aria-expanded="true"
                    x-bind:aria-activedescendant="activeId"
                    x-model="query"
                    x-on:input="filter()"
                    x-on:keydown.down.prevent="move(1)"
                    x-on:keydown.up.prevent="move(-1)"
                    x-on:keydown.enter.prevent="activeEl()?.click()"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    data-scope="command"
                    data-part="input"
                >
                <kbd data-scope="command" data-part="esc">esc</kbd>
            </div>

            <div id="sigta-command-list" role="listbox" data-scope="command" data-part="list">
                {{ $slot }}
                <div x-cloak x-show="matches === 0" data-scope="command" data-part="empty">Nothing found — try different ink.</div>
            </div>
        </div>
    </dialog>
</div>
