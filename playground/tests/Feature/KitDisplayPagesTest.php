<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KitDisplayPagesTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function pages(): array
    {
        return [
            'badge' => ['badge', 'data-sigta="badge"'],
            'card' => ['card', 'data-sigta="card"'],
            'callout' => ['callout', 'data-sigta="callout"'],
            'avatar' => ['avatar', 'data-sigta="avatar"'],
            'skeleton' => ['skeleton', 'data-sigta="skeleton"'],
            'stat' => ['stat', 'data-sigta="stat"'],
        ];
    }

    #[DataProvider('pages')]
    public function test_display_kit_page_renders(string $page, string $marker): void
    {
        $response = $this->get('/kit/'.$page);

        $response->assertStatus(200);
        $response->assertSee($marker, false);
    }
}
