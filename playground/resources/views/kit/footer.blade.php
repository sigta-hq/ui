<x-kit-shell title="Section · Footer">
    <x-kit-example label="Site footer: your brand, tagline, nav columns and signoff">
    «sigta:section.footer
        tagline="A small studio building durable software."
        signoff="© 2026 Acme Inc."
        :columns="[
            ['heading' => 'Product', 'links' => [
                ['label' => 'Features', 'href' => '#features'],
                ['label' => 'Pricing', 'href' => '#pricing'],
            ]],
            ['heading' => 'Contact', 'links' => [
                ['label' => 'Write to us', 'href' => 'mailto:hello@example.com'],
            ]],
        ]">
        «x-slot:brand>«sigta:brand name="Acme">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>«/x-slot:brand>
    «/sigta:section.footer>
    </x-kit-example>

    <x-kit-example label="Three columns (one row from lg)">
    «sigta:section.footer
        :columns="[
            ['heading' => 'Navigation', 'links' => [
                ['label' => 'How we help', 'href' => '#help'],
                ['label' => 'Pricing', 'href' => '#pricing'],
            ]],
            ['heading' => 'Services', 'links' => [
                ['label' => 'Technical review', 'href' => '#review'],
                ['label' => 'Extra developer', 'href' => '#developer'],
            ]],
            ['heading' => 'Contact', 'links' => [
                ['label' => 'hello@example.com', 'href' => 'mailto:hello@example.com'],
            ]],
        ]">
        «x-slot:brand>«sigta:brand name="Acme">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>«/x-slot:brand>
    «/sigta:section.footer>
    </x-kit-example>
</x-kit-shell>
