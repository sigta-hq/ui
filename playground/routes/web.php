<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/docs');
});

// Internal usage & theming docs (distinct from the /kit component gallery).
Route::view('/docs', 'docs');

Route::get('/kit/{page?}', function (?string $page = null) {
    $view = 'kit.'.($page ?? 'index');

    abort_unless(view()->exists($view), 404);

    return view($view);
})->where('page', '[a-z0-9\-]+');
