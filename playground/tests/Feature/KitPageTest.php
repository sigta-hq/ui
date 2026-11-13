<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
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

    public function test_kit_scrollbar_page_renders_scroll_areas(): void
    {
        $response = $this->get('/kit/scrollbar');

        $response->assertStatus(200);
        $response->assertSee('overflow-y-auto', false);
        $response->assertSee('overflow-x-auto', false);
        $response->assertSee('data-variant="inverse"', false);
    }

    public function test_unknown_kit_page_is_404(): void
    {
        $this->get('/kit/nope')->assertStatus(404);
    }

    /**
     * A broken snippet 500s its whole page, so every kit page must render.
     *
     * @return array<string, array{string}>
     */
    public static function kitPages(): array
    {
        $pages = [];

        foreach (glob(dirname(__DIR__, 2).'/resources/views/kit/*.blade.php') as $file) {
            $page = basename($file, '.blade.php');
            $pages[$page] = [$page];
        }

        return $pages;
    }

    #[DataProvider('kitPages')]
    public function test_every_kit_page_renders(string $page): void
    {
        $this->get('/kit/'.$page)->assertStatus(200);
    }
}
