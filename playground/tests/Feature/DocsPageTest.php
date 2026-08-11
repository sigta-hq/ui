<?php

namespace Tests\Feature;

use Tests\TestCase;

class DocsPageTest extends TestCase
{
    public function test_docs_page_renders(): void
    {
        $this->get('/docs')->assertStatus(200);
    }

    public function test_docs_page_shows_install_instructions(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertSee('composer require sigta/ui');
        // The @import + @sigtaScripts wiring shown as escaped source.
        $response->assertSee('vendor/sigta/ui/resources/css/sigta.css');
        $response->assertSee('@sigtaScripts');
        // Theming section documents the runtime attribute.
        $response->assertSee('data-sigta-theme', false);
    }

    public function test_docs_page_renders_live_package_components(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertSee('data-sigta="button"', false);
        $response->assertSee('data-sigta="input"', false);
        $response->assertSee('data-sigta="callout"', false);
        $response->assertSee('data-sigta="badge"', false);
        $response->assertSee('data-sigta="brand"', false);
    }

    public function test_docs_page_shows_prefix_syntax_as_escaped_source(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        // The preferred <sigta:…> prefix appears as escaped text, not compiled away.
        $response->assertSee('&lt;sigta:button', false);
    }

    public function test_docs_page_lists_component_kit_links(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertSee('href="/kit/button"', false);
        $response->assertSee('href="/kit/callout"', false);
        $response->assertSee('href="/kit/theming"', false);
    }

    public function test_docs_page_contains_no_raw_hex_colors(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        // Livewire's progress-bar hex is overridden to var(--sigta-accent) via
        // config/livewire.php, so a clean page carries zero raw hex.
        $this->assertDoesNotMatchRegularExpression(
            '/#[0-9A-Fa-f]{6}/',
            $response->getContent(),
            'Docs page response must not contain raw hex colors — use var(--sigta-*) or tokens.',
        );
    }

    public function test_theming_kit_page_renders_with_studio_theme(): void
    {
        $response = $this->get('/kit/theming');

        $response->assertStatus(200);
        $response->assertSee('data-sigta-theme="studio"', false);
        // Interactive Alpine toggle is present.
        $response->assertSee('x-data', false);
        $response->assertSee('data-sigta="segmented"', false);
        // Cluster renders real components under the theme.
        $response->assertSee('data-sigta="card"', false);
        $response->assertSee('data-sigta="stat"', false);
    }
}
