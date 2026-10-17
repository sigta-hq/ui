{{-- Brand FAQ: section-header + accordion. The accordion `name` makes it a
     single-open group (native <details name>); items come from `:items`
     (array of ['q' => …, 'a' => …]). The default entries live in
     packages/contract/src/sections/faq.json, which the Svelte section reads
     too. --}}
@props([
    'heading' => 'Spørgsmål, vi ofte får.',
    'lede' => 'Og de ærlige svar. Mangler dit, så spørg løs på intro-samtalen.',
    'name' => 'faq',
    'items' => \Sigta\Ui\Sections::get('faq', 'items'),
])

<section data-sigta="section.faq" {{ $attributes }}>
    <div data-scope="section.faq" data-part="wrap">
        <x-sigta::section-header
            data-scope="section.faq"
            data-part="header"
            :heading="$heading"
            :lede="$lede" />
        <x-sigta::accordion :name="$name" data-scope="section.faq" data-part="accordion">
            @foreach ($items as $item)
                <x-sigta::accordion.item :heading="$item['q']">
                    <p>{{ $item['a'] }}</p>
                </x-sigta::accordion.item>
            @endforeach
        </x-sigta::accordion>
    </div>
</section>
