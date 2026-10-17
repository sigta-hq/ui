<!-- The Sigta landing page in Svelte: the twin of landing.blade.php. -->
<script>
    import { Brand, Button, Navbar, Navlink, Paper, Rule, Section } from '@sigta-hq/svelte';
</script>

<a href="#main" class="absolute top-0 -left-[9999px] focus:left-0 z-[100] bg-ink text-surface font-mono text-[0.8rem] no-underline px-[1em] py-[0.6em] rounded-br-base">Til indholdet</a>

<Paper />

<div class="relative z-10">
    <Navbar>
        {#snippet brand()}
            <a href="#" aria-label="Sigta forside" class="no-underline"><Brand /></a>
        {/snippet}

        <Navlink href="#hjaelp">Sådan hjælper vi</Navlink>
        <Navlink href="#priser">Priser</Navlink>
        <Navlink href="#om">Om Sigta</Navlink>
        <Navlink href="#kontakt">Kontakt</Navlink>

        {#snippet actions()}
            <Button variant="cta" href="https://calendly.com/sf-sigta/30min" target="_blank" class="group">
                Book en intro-samtale <span class="transition-transform duration-[180ms] ease-out group-hover:translate-x-[3px]" aria-hidden="true">→</span>
            </Button>
        {/snippet}
    </Navbar>

    <main id="main">
        <Section.Hero />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><Rule variant="1" class="my-[clamp(48px,7vw,96px)]" /></div>

        <Section.Recognition id="genkend" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><Rule variant="2" class="my-[clamp(48px,7vw,96px)]" /></div>

        <Section.Features id="hjaelp" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><Rule variant="3" class="my-[clamp(48px,7vw,96px)]" /></div>

        <Section.Pricing id="priser" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><Rule variant="2" class="my-[clamp(48px,7vw,96px)]" /></div>

        <Section.About id="om" />

        <Section.Proof />

        <Section.Quote />

        <Section.Faq id="faq" />

        <Section.Cta id="kontakt" target="_blank" />
    </main>

    <Section.Footer />
</div>
