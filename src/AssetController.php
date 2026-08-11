<?php

namespace Sigta\Ui;

use Symfony\Component\HttpFoundation\Response;

class AssetController
{
    public function __invoke(): Response
    {
        $path = __DIR__.'/../dist/sigta.js';

        return new Response(file_get_contents($path), 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
