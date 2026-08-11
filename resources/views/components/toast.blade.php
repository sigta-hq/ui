{{-- Toast region. Include ONCE per layout, near </body>.
     Trigger from Livewire: $this->dispatch('sigta-toast', heading: 'Saved', variant: 'success');
     Trigger from Alpine/JS: $dispatch('sigta-toast', { text: '…', variant: 'danger' }) --}}
@props([
    'position' => 'bottom-end',
])

@php
$placement = match ($position) {
    'bottom-start' => 'bottom-0 left-0 items-start',
    'top-end' => 'top-0 right-0 items-end',
    'top-start' => 'top-0 left-0 items-start',
    default => 'bottom-0 right-0 items-end',
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
                'text-ink': toast.variant === 'info',
            }" aria-hidden="true">
                <template x-if="toast.variant === 'success'"><x-sigta::icon name="circle-check" size="sm" /></template>
                <template x-if="toast.variant === 'danger'"><x-sigta::icon name="triangle-alert" size="sm" /></template>
                <template x-if="toast.variant === 'info'"><x-sigta::icon name="info" size="sm" /></template>
            </span>
            <span class="text-[0.92rem] leading-snug">
                <b x-show="toast.heading" x-text="toast.heading" class="font-head font-semibold block"></b>
                <span x-text="toast.text"></span>
            </span>
            <button type="button" x-on:click="remove(toast.id)" aria-label="Dismiss"
                    class="ml-auto -mr-1 p-1 rounded-base text-graphite hover:text-ink cursor-pointer">
                <x-sigta::icon name="x" size="xs" />
            </button>
        </div>
    </template>
</div>
