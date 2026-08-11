<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardPageTest extends TestCase
{
    public function test_dashboard_renders_from_library_components(): void
    {
        $response = $this->get('/kit/dashboard');

        $response->assertStatus(200);
        // App shell + its parts
        $response->assertSee('data-sigta="shell"', false);
        $response->assertSee('data-sigta="shell.bar"', false);
        $response->assertSee('data-sigta="shell.link"', false);
        // Stats, chart, segmented, pill
        $response->assertSee('data-sigta="stat"', false);
        $response->assertSee('data-sigta="chart.line"', false);
        $response->assertSee('data-sigta="segmented"', false);
        // Content from the brand spotlight
        $response->assertSee('Plumb');
        $response->assertSee('2,418');
        $response->assertSee('On track');
    }

    public function test_dashboard_has_no_raw_hex_colors(): void
    {
        $body = $this->get('/kit/dashboard')->getContent();

        // Livewire's injected progress bar color is overridden in config/livewire.php.
        $this->assertDoesNotMatchRegularExpression('/#[0-9A-Fa-f]{6}\b/', $body);
    }
}
