@props([
    'variant' => 'secondary',
    'size' => 'base',
])

@php
$classes = 'inline-flex items-center gap-[0.55em] whitespace-nowrap font-mono tracking-[0.04em] no-underline cursor-pointer rounded-base border-[1.5px] transition duration-150 ease-out disabled:opacity-40 disabled:pointer-events-none aria-disabled:opacity-40 aria-disabled:pointer-events-none';

$classes .= ' '.match ($size) {
    'sm' => 'text-[0.72rem] px-[1.1em] py-[0.6em]',
    default => 'text-[0.82rem] px-[1.4em] py-[0.78em]',
};

$classes .= ' '.match ($variant) {
    'primary', 'cta' => 'bg-ink text-surface border-ink hover:-translate-y-[2px] hover:shadow-paper',
    'ghost' => 'border-transparent px-[0.6em] text-ink hover:border-line hover:bg-surface-raised',
    'danger' => 'bg-danger text-surface-raised border-danger hover:-translate-y-[2px]',
    // Paper fill for dark/ink bands — the CTA button that sits on bg-inverse.
    'inverse' => 'bg-surface text-ink border-surface hover:-translate-y-[2px] hover:shadow-paper',
    // Transparent outline on dark/ink bands — hairline border, paper text, faint tint on hover.
    'ghost-inverse' => 'bg-transparent text-inverse-fg border-inverse-line hover:bg-inverse-fg/10 hover:-translate-y-[2px]',
    default => 'bg-transparent text-ink border-ink hover:bg-ink/5 hover:-translate-y-[2px]',
};

$linkAttributes = ['class' => $classes];
if ($attributes->get('target') === '_blank' && ! $attributes->has('rel')) {
    $linkAttributes['rel'] = 'noopener';
}
@endphp

@if ($attributes->has('href'))
    <a data-sigta="button" {{ $attributes->merge($linkAttributes) }}>{{ $slot }}</a>
@else
    <button data-sigta="button" {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
