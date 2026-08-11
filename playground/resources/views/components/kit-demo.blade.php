@props(['label'])

<section class="bg-surface-raised border border-line rounded-panel p-6" data-kit-demo>
    <h2 class="font-mono text-[0.66rem] tracking-[0.14em] uppercase text-graphite font-normal">{{ $label }}</h2>
    <div class="mt-5">
        {{ $slot }}
    </div>
</section>
