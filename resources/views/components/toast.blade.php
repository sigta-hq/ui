{{-- Toast region. Include ONCE per layout, near </body>.
     Trigger from Livewire: Sigta::toast('Saved') or $this->dispatch('sigta-toast', variant: 'success')
     Trigger from Alpine/JS: $sigta.toast('Saved') or window.Sigta.toast({ text: '…', variant: 'danger' })
     With wire:navigate, wrap in @persist('toast') so toasts survive page transitions.
     Bottom placements rise above a visible floating bar rather than covering it. --}}
@props([
    'position' => 'bottom-end',
])

@php
$placement = match ($position) {
    'bottom-start' => 'bottom-0 left-0 items-start mb-[var(--sigta-floating-space,0px)]',
    'top-end' => 'top-0 right-0 items-end',
    'top-start' => 'top-0 left-0 items-start',
    default => 'bottom-0 right-0 items-end mb-[var(--sigta-floating-space,0px)]',
};
@endphp

<div
    x-data="sigtaToasts"
    x-on:sigta-toast.window="add($event.detail)"
    {{ $attributes->merge(['class' => 'fixed z-[90] p-5 flex flex-col gap-2.5 pointer-events-none '.$placement]) }}
    data-sigta="toast"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-bind:role="toast.variant === 'danger' ? 'alert' : 'status'"
            class="pointer-events-auto flex items-start gap-3 min-w-64 max-w-sm bg-surface-raised border-[1.5px] rounded-[5px] px-4 py-3 shadow-paper border-l-4"
            x-bind:class="{
                'border-success': toast.variant === 'success',
                'border-danger': toast.variant === 'danger',
                'border-warning': toast.variant === 'warning',
                'border-ink': toast.variant === 'info',
            }"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <span class="mt-0.5 shrink-0" x-bind:class="{
                'text-success': toast.variant === 'success',
                'text-danger': toast.variant === 'danger',
                'text-warning': toast.variant === 'warning',
                'text-ink': toast.variant === 'info',
            }" aria-hidden="true">
                <template x-if="toast.variant === 'success'"><x-sigta::icon name="circle-check" size="sm" /></template>
                <template x-if="toast.variant === 'danger'"><x-sigta::icon name="triangle-alert" size="sm" /></template>
                <template x-if="toast.variant === 'warning'"><x-sigta::icon name="circle-alert" size="sm" /></template>
                <template x-if="toast.variant === 'info'"><x-sigta::icon name="info" size="sm" /></template>
            </span>
            <span class="text-[0.92rem] leading-snug flex-1 min-w-0">
                <b x-show="toast.heading" x-text="toast.heading" class="font-head font-semibold block"></b>
                <span x-text="toast.text"></span>
                <template x-if="toast.link && toast.link.href">
                    <a
                        x-bind:href="toast.link.href"
                        x-bind:target="toast.link.target || null"
                        x-bind:rel="toast.link.rel || null"
                        x-bind:download="toast.link.download || null"
                        x-bind="toast.link.navigate ? { 'wire:navigate': true } : {}"
                        x-text="toast.link.text || 'View'"
                        class="block mt-1.5 font-mono text-[0.72rem] uppercase tracking-[0.12em] text-ink underline underline-offset-2 hover:no-underline"
                    ></a>
                </template>
            </span>
            <button type="button" x-on:click="remove(toast.id)" aria-label="Dismiss"
                    class="ml-auto -mr-1 p-1 rounded-base text-graphite hover:text-ink cursor-pointer">
                <x-sigta::icon name="x" size="xs" />
            </button>
        </div>
    </template>
</div>
