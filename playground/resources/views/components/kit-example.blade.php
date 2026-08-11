{{-- A kit demo that shows a component AND its copyable source from one snippet.

     Author the example once in the default slot, writing « for every < so the
     sigta: prefix precompiler (which rewrites <sigta: across the raw file,
     @php strings included) leaves it as text. This unescapes « → <, renders it
     live, and — behind a code-icon toggle — prints the literal source.

       <x-kit-example label="Primary">
       «sigta:button variant="primary">Save draft«/sigta:button>
       </x-kit-example>

     Note: the source string is computed inline in each echo rather than in an
     @php block — nesting Blade::render() inside a component that is itself
     being rendered clobbers @php-local variables, so we avoid locals here.
--}}
@props(['label' => null, 'bleed' => null])

{{-- Section/page demos render edge-to-edge (they carry their own gutter and
     max-width); components get reading padding. `bleed` is resolved INLINE in
     the preview div's class below, never in an @php local — rendering the code
     icon re-enters Blade and clobbers @php-assigned locals, but @props values
     ($bleed, $slot) survive. Pass `bleed` explicitly to override auto-detect. --}}

<section
    class="bg-surface-raised border border-line rounded-panel overflow-hidden"
    data-kit-example
    x-data="{ show: false }"
>
    {{-- header: label + code toggle --}}
    <div class="flex items-center justify-between gap-4 px-5 pt-4">
        <h2 class="font-mono text-[0.66rem] tracking-[0.14em] uppercase text-graphite font-normal">{{ $label ?? 'Example' }}</h2>
        <button
            type="button"
            x-on:click="show = ! show"
            x-bind:aria-expanded="show"
            aria-label="Toggle source"
            class="inline-flex items-center gap-1.5 rounded-base border border-line px-2 py-1 font-mono text-[0.62rem] tracking-[0.04em] uppercase text-graphite hover:text-ink hover:border-ink cursor-pointer transition-colors"
            x-bind:class="show ? 'bg-ink text-surface border-ink hover:text-surface' : ''"
        >
            <x-sigta::icon name="code" size="xs" />
            <span x-text="show ? 'Hide' : 'Code'"></span>
        </button>
    </div>

    {{-- live preview — sections bleed (own gutter); components get reading padding --}}
    <div class="{{ ($bleed ?? in_array(request()->segment(2), array_merge((require base_path('vendor/sigta/ui/resources/catalog.php'))['sections']['items'] ?? [], (require base_path('vendor/sigta/ui/resources/catalog.php'))['pages']['items'] ?? []), true)) ? 'py-5' : 'p-5' }}">
        {!! Illuminate\Support\Facades\Blade::render(trim(str_replace('«', '<', $slot->toHtml()))) !!}
    </div>

    {{-- source (folds out) --}}
    <div
        x-show="show"
        x-collapse.duration.200ms
        x-cloak
        class="border-t border-line bg-inverse text-inverse-fg"
    >
        <div class="flex items-center justify-between px-4 py-2 border-b border-inverse-line">
            <span class="font-mono text-[0.62rem] tracking-[0.14em] uppercase text-inverse-fg-muted">Source</span>
            <button
                type="button"
                class="font-mono text-[0.66rem] text-inverse-fg-muted hover:text-inverse-fg cursor-pointer inline-flex items-center gap-1.5"
                x-data="{ copied: false }"
                x-on:click="navigator.clipboard.writeText($refs.code.textContent.trim()); copied = true; setTimeout(() => copied = false, 1400)"
            >
                <span x-text="copied ? 'copied' : 'copy'"></span>
            </button>
        </div>
        <pre class="overflow-x-auto px-4 py-3 text-[0.78rem] leading-relaxed"><code x-ref="code" class="font-mono">{{ trim(str_replace('«', '<', $slot->toHtml())) }}</code></pre>
    </div>
</section>
