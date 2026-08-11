<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_renders_with_key_content(): void
    {
        $response = $this->get('/kit/landing');

        $response->assertStatus(200);
        $response->assertSee('Book en intro-samtale');
        $response->assertSee('800 kr.');
        $response->assertSee('CVR 45530760');
    }

    public function test_landing_page_is_built_from_package_components(): void
    {
        $response = $this->get('/kit/landing');

        $response->assertStatus(200);
        $response->assertSee('data-sigta="navbar"', false);
        $response->assertSee('data-sigta="brand"', false);
        $response->assertSee('data-sigta="button"', false);
        $response->assertSee('data-sigta="accordion"', false);
        $response->assertSee('data-sigta="accordion.item"', false);
    }

    public function test_landing_page_contains_no_raw_hex_colors(): void
    {
        $response = $this->get('/kit/landing');

        $response->assertStatus(200);
        $this->assertDoesNotMatchRegularExpression(
            '/#[0-9A-Fa-f]{6}/',
            $response->getContent(),
            'Landing page response must not contain raw hex colors — use var(--sigta-*) or currentColor.',
        );
    }
}
