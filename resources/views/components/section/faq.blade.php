{{-- FAQ: section-header + accordion. The accordion `name` makes it a
     single-open group (native <details name>); items come from `:items`
     (array of ['q' => …, 'a' => …]). Ships no copy. --}}
@props([
    'heading' => null,
    'lede' => null,
    'name' => 'faq',
    'items' => [],
])

<section data-sigta="section.faq" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        @if ($heading || $lede)
            <x-sigta::section-header
                class="mb-[clamp(28px,4vw,52px)]"
                :heading="$heading"
                :lede="$lede" />
        @endif
        <x-sigta::accordion :name="$name" class="mx-auto max-w-[760px]">
            @foreach ($items as $item)
                <x-sigta::accordion.item :heading="$item['q']">
                    <p>{{ $item['a'] }}</p>
                </x-sigta::accordion.item>
            @endforeach
        </x-sigta::accordion>
    </div>
</section>
