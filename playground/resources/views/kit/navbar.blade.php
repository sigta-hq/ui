<x-kit-shell title="Navbar">
    <x-kit-example label="Brand header — brand / links / actions">
    «div class="-m-6 overflow-hidden rounded-panel">
        «sigta:navbar>
            «x-slot:brand>
                «a href="#top" class="no-underline">«sigta:brand name="Acme" size="sm">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>«/a>
            «/x-slot:brand>

            «sigta:navlink href="#work" current>Work«/sigta:navlink>
            «sigta:navlink href="#studio">Studio«/sigta:navlink>
            «sigta:navlink href="#journal">Journal«/sigta:navlink>
            «sigta:navlink href="#contact">Contact«/sigta:navlink>

            «x-slot:actions>
                «sigta:button variant="cta" size="sm" href="#book" target="_blank">Book a project«/sigta:button>
            «/x-slot:actions>
        «/sigta:navbar>
        «div class="h-24 bg-surface">«/div>
    «/div>
    </x-kit-example>

    <x-kit-example label="CTA via props — target like a button">
    «div class="-m-6 overflow-hidden rounded-panel">
        «sigta:navbar cta-label="Book a project" cta-href="#book" target="_blank">
            «x-slot:brand>
                «a href="#top" class="no-underline">«sigta:brand name="Acme" size="sm">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>«/a>
            «/x-slot:brand>

            «sigta:navlink href="#work">Work«/sigta:navlink>
            «sigta:navlink href="#studio">Studio«/sigta:navlink>
        «/sigta:navbar>
        «div class="h-24 bg-surface">«/div>
    «/div>
    </x-kit-example>

    <x-kit-example label="collapse=&quot;lg&quot;: longer links fold behind the burger up to lg">
    «div class="-m-6 overflow-hidden rounded-panel">
        «sigta:navbar collapse="lg" cta-label="Book a call" cta-href="#book" target="_blank">
            «x-slot:brand>
                «a href="#top" class="no-underline">«sigta:brand name="Acme" size="sm">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>«/a>
            «/x-slot:brand>

            «sigta:navlink href="#raadgivning" current>Rådgivning«/sigta:navlink>
            «sigta:navlink href="#hjaelp">Sådan hjælper vi«/sigta:navlink>
            «sigta:navlink href="#priser">Priser«/sigta:navlink>
            «sigta:navlink href="#om">Om Sigta«/sigta:navlink>
        «/sigta:navbar>
        «div class="h-24 bg-surface">«/div>
    «/div>
    </x-kit-example>

    <x-kit-example label="Navlinks — hover underline, current state">
    «div class="flex items-center gap-8">
        «sigta:navlink href="#work" current>Current page«/sigta:navlink>
        «sigta:navlink href="#studio">Hover me«/sigta:navlink>
        «sigta:navlink href="#journal">Another link«/sigta:navlink>
    «/div>
    </x-kit-example>

    <x-kit-example label="Mobile">
    «p class="font-body text-graphite text-[0.9rem]">Below «code class="font-mono text-[0.8rem]">md«/code> (or «code class="font-mono text-[0.8rem]">lg«/code> with «code class="font-mono text-[0.8rem]">collapse="lg"«/code>) the links collapse behind a hamburger (Alpine «code class="font-mono text-[0.8rem]">x-data«/code> toggle, «code class="font-mono text-[0.8rem]">aria-expanded«/code> wired). Resize the viewport on the demo above to see it.«/p>
    </x-kit-example>
</x-kit-shell>
