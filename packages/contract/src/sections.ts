/* The brand-default content of the page sections: the repeated items a
   section renders when it is given none (FAQ entries, service tiles, pricing
   tiers, footer columns…). Both runtimes read the same JSON files in
   ./sections/ — the Svelte sections import this module, the Blade views read
   them through Sigta\Ui\Sections — so a default can never differ between the
   runtimes. Edit the JSON, never a copy. */
import about from './sections/about.json' with { type: 'json' };
import faq from './sections/faq.json' with { type: 'json' };
import features from './sections/features.json' with { type: 'json' };
import footer from './sections/footer.json' with { type: 'json' };
import hero from './sections/hero.json' with { type: 'json' };
import pricing from './sections/pricing.json' with { type: 'json' };
import recognition from './sections/recognition.json' with { type: 'json' };

/** A section.faq entry: the question and its answer. */
export interface FaqItem { q: string; a: string }

/** A section.features tile. `icon` is inline `<svg…>` markup or an icon name. */
export interface FeatureItem { num?: string | null; icon?: string | null; title?: string | null; text?: string | null }

/** A section.footer column: a mono heading over a list of links. */
export interface FooterColumn { heading: string; links: { label: string; href: string }[] }

/** A section.hero inline figure. */
export interface HeroStat { value?: string | number | null; label?: string | null }

/** A section.pricing tier. `mark` highlights the figure; `annot` overhangs the panel as a hand-note. */
export interface PricingTier {
    label?: string | null;
    title?: string | null;
    figure?: string | null;
    figureSuffix?: string | null;
    mark?: boolean;
    annot?: string | null;
    body?: string[];
}

/** A section.recognition card: a mono kicker, the scene (a quote) and the answer. */
export interface RecognitionItem { num?: string | null; scene?: string | null; answer?: string | null }

export const sections: {
    about: { paragraphs: string[] };
    faq: { items: FaqItem[] };
    features: { items: FeatureItem[] };
    footer: { columns: FooterColumn[] };
    hero: { stats: HeroStat[] };
    pricing: { tiers: PricingTier[] };
    recognition: { items: RecognitionItem[] };
} = { about, faq, features, footer, hero, pricing, recognition };
