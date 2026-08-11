<x-kit-shell title="Section · Proof">
    <x-kit-example label="Proof strip — zero-config (years stat + placeholder logos)">
    «sigta:section.proof />
    </x-kit-example>

    <x-kit-example label="Custom years + logos">
    «sigta:section.proof
        years-value="20 år"
        years-label="i branchen"
        :logos="['Acme A/S', 'Foobar ApS', 'Baz Group']" />
    </x-kit-example>
</x-kit-shell>
