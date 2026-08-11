<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChatPageTest extends TestCase
{
    public function test_chat_page_renders_from_library_components(): void
    {
        $response = $this->get('/kit/chat');

        $response->assertStatus(200);
        // App frame + composer + brand + dropdown come from the library.
        $response->assertSee('data-sigta="shell"', false);
        $response->assertSee('data-sigta="composer"', false);
        $response->assertSee('data-sigta="brand"', false);
        // Chat chrome
        $response->assertSee('Message Sigta', false);
        $response->assertSee('How can I help', false);
        $response->assertSee('sigta-composer-submit', false);
    }

    public function test_chat_page_has_no_raw_hex_colors(): void
    {
        $body = $this->get('/kit/chat')->getContent();

        $this->assertDoesNotMatchRegularExpression('/#[0-9A-Fa-f]{6}\b/', $body);
    }
}
