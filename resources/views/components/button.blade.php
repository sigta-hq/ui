@props([
    'variant' => 'secondary',
    'size' => 'base',
    'loading' => null,
])

@php
// Pressed, a lifted button lands back on the paper and gives a little; let go,
// it springs back up. The spring curve only overshoots by a hair. Livewire
// disables a submitting form's buttons only to stop a second submit, so those
// keep their ink rather than dimming like a disabled one.
$classes = 'relative inline-flex items-center gap-[0.55em] whitespace-nowrap font-mono tracking-[0.04em] no-underline cursor-pointer rounded-base border-[1.5px] transition duration-150 ease-sigta-spring active:scale-[0.97] active:duration-[var(--sigta-dur-press,90ms)] active:ease-out disabled:not-in-data-loading:opacity-40 disabled:pointer-events-none aria-disabled:opacity-40 aria-disabled:pointer-events-none';

$classes .= ' '.match ($size) {
    'sm' => 'text-[0.72rem] px-[1.1em] py-[0.6em]',
    default => 'text-[0.82rem] px-[1.4em] py-[0.78em]',
};

$classes .= ' '.match ($variant) {
    'primary', 'cta' => 'bg-ink text-surface border-ink hover:-translate-y-[2px] hover:shadow-paper active:translate-y-0 active:shadow-none',
    'ghost' => 'border-transparent px-[0.6em] text-ink hover:border-line hover:bg-surface-raised active:bg-ink/10',
    'danger' => 'bg-danger text-surface-raised border-danger hover:-translate-y-[2px] active:translate-y-0',
    // Paper fill for dark/ink bands — the CTA button that sits on bg-inverse.
    'inverse' => 'bg-surface text-ink border-surface hover:-translate-y-[2px] hover:shadow-paper active:translate-y-0 active:shadow-none',
    // Transparent outline on dark/ink bands — hairline border, paper text, faint tint on hover.
    'ghost-inverse' => 'bg-transparent text-inverse-fg border-inverse-line hover:bg-inverse-fg/10 hover:-translate-y-[2px] active:translate-y-0',
    default => 'bg-transparent text-ink border-ink hover:bg-ink/5 hover:-translate-y-[2px] active:translate-y-0 active:bg-ink/10',
};

$linkAttributes = ['class' => $classes];
if ($attributes->get('target') === '_blank' && ! $attributes->has('rel')) {
    $linkAttributes['rel'] = 'noopener';
}

// While Livewire runs a button's action it sets data-loading on the button (on
// the form for a submit). The label never gives way: once the action outlasts
// a beat the runtime marks the button data-sigta-busy and the compass stands
// in for its leading icon, or is written in before a label that has none, so
// a quick action shows nothing. The loader markup is added only where that
// can happen, unless `loading` says otherwise: true shows it now (a busy
// button you drive yourself), false never.
$busy = $loading ?? ($attributes->get('type') === 'submit' || collect(array_keys($attributes->getAttributes()))->contains(
    fn (string $name) => str_starts_with($name, 'wire:click') || str_starts_with($name, 'wire:submit') || in_array($name, ['data-loading', 'x-bind:data-loading', ':data-loading'], true)
));

$buttonAttributes = ['type' => 'button', 'class' => $classes];
if ($busy && str_starts_with(ltrim((string) $slot), '<svg')) {
    $buttonAttributes['data-lead-icon'] = '';
}
if ($loading === true) {
    $buttonAttributes['data-loading'] = 'true';
    $buttonAttributes['data-sigta-busy'] = '';
    $buttonAttributes['aria-busy'] = 'true';
}
@endphp

@if ($attributes->has('href'))
    <a data-sigta="button" {{ $attributes->merge($linkAttributes) }}>{{ $slot }}</a>
@else
    <button data-sigta="button" {{ $attributes->merge($buttonAttributes) }}>
        @if ($busy)
            <span class="sigta-button-busy" aria-hidden="true"><x-sigta::loader size="xs" decorative /></span>
            <span class="sigta-button-label contents">{{ $slot }}</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
