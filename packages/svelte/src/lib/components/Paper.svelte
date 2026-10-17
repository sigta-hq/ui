<script lang="ts">
    /* Paper — the page-ground overlay: dot grid + paper grain. Place once,
       directly inside <body>. Like the Blade view it takes no other attributes. */
    interface Props {
        dots?: boolean;
        grain?: boolean;
    }

    let { dots = true, grain = true }: Props = $props();
</script>

<div aria-hidden="true" data-sigta="paper">
    {#if dots}
        <div data-scope="paper" data-part="dots"></div>
    {/if}
    {#if grain}
        <div data-scope="paper" data-part="grain">
            <svg data-scope="paper" data-part="noise">
                <filter id="sigta-grain"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" stitchTiles="stitch" /></filter>
                <rect width="100%" height="100%" filter="url(#sigta-grain)" />
            </svg>
        </div>
    {/if}
</div>
