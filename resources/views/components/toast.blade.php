{{-- Toast region. Include ONCE per layout, near </body>.
     Trigger from Livewire: Sigta::toast('Saved') or $this->dispatch('sigta-toast', variant: 'success')
     Trigger from Alpine/JS: $sigta.toast('Saved') or window.Sigta.toast({ text: '…', variant: 'danger' })
     An action adds a button that fires an event and closes the toast — the
     "Undo" after a completion: action: { text: 'Undo', event: 'task-reopen', detail: { id: 5 } }
     With wire:navigate, wrap in @persist('toast') so toasts survive page transitions.
     Bottom placements rise above a visible floating bar rather than covering it. --}}
@props([
    'position' => 'bottom-end',
])

<div
    x-data="sigtaToasts"
    x-on:sigta-toast.window="add($event.detail)"
    {{ $attributes->merge(['data-position' => $position]) }}
    data-sigta="toast"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-bind:role="toast.variant === 'danger' ? 'alert' : 'status'"
            x-bind:data-variant="toast.variant"
            x-bind:data-leaving="toast.leaving ? '' : null"
            data-scope="toast"
            data-part="item"
        >
            <span data-scope="toast" data-part="icon" aria-hidden="true">
                <template x-if="toast.variant === 'success'"><x-sigta::icon name="circle-check" size="sm" /></template>
                <template x-if="toast.variant === 'danger'"><x-sigta::icon name="triangle-alert" size="sm" /></template>
                <template x-if="toast.variant === 'warning'"><x-sigta::icon name="circle-alert" size="sm" /></template>
                <template x-if="toast.variant === 'info'"><x-sigta::icon name="info" size="sm" /></template>
            </span>
            <span data-scope="toast" data-part="body">
                <b x-show="toast.heading" x-text="toast.heading" data-scope="toast" data-part="heading"></b>
                <span x-text="toast.text"></span>
                <template x-if="toast.link && toast.link.href">
                    <a
                        x-bind:href="toast.link.href"
                        x-bind:target="toast.link.target || null"
                        x-bind:rel="toast.link.rel || null"
                        x-bind:download="toast.link.download || null"
                        x-bind="toast.link.navigate ? { 'wire:navigate': true } : {}"
                        x-text="toast.link.text || 'View'"
                        data-scope="toast"
                        data-part="link"
                    ></a>
                </template>
                <template x-if="toast.action && toast.action.text">
                    <button
                        type="button"
                        x-on:click="act(toast)"
                        x-text="toast.action.text"
                        data-scope="toast"
                        data-part="action"
                    ></button>
                </template>
            </span>
            <button type="button" x-on:click="remove(toast.id)" aria-label="Dismiss" data-scope="toast" data-part="dismiss">
                <x-sigta::icon name="x" size="xs" />
            </button>
        </div>
    </template>
</div>
