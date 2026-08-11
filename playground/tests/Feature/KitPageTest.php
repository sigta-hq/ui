<?php

namespace Tests\Feature;

use Tests\TestCase;

class KitPageTest extends TestCase
{
    public function test_kit_index_renders(): void
    {
        $response = $this->get('/kit');

        $response->assertStatus(200);
        $response->assertSee('data-sigta="brand"', false);
        $response->assertSee('/sigta/sigta.js');
    }

    public function test_kit_button_page_renders_package_components(): void
    {
        $response = $this->get('/kit/button');

        $response->assertStatus(200);
        $response->assertSee('data-sigta="button"', false);
        $response->assertSee('Save draft');
    }

    public function test_unknown_kit_page_is_404(): void
    {
        $this->get('/kit/nope')->assertStatus(404);
    }
}
