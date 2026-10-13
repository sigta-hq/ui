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

// Stand-ins for the endpoints a host app gives the editor, so the kit page can
// show pictures and titled links working end to end without a real store.
Route::get('/kit-api/sample-image', function () {
    return response()->file(dirname(__DIR__, 2).'/resources/images/sigta-mark.svg', ['Content-Type' => 'image/svg+xml']);
});

Route::post('/kit-api/upload', function () {
    request()->validate(['file' => ['required', 'file', 'image', 'max:10240']]);

    return response()->json(['url' => '/kit-api/sample-image', 'name' => request()->file('file')->getClientOriginalName()]);
});

Route::get('/kit-api/resolve', function () {
    $path = parse_url((string) request()->query('url'), PHP_URL_PATH) ?: '/';

    return response()->json(['title' => 'Kit: '.ucfirst(trim(basename($path), '/') ?: 'home'), 'url' => request()->query('url')]);
});

Route::get('/kit-api/mentions', function () {
    $query = mb_strtolower(trim((string) request()->query('q')));

    $people = collect(['Astrid Holm', 'Bo Lindqvist', 'Freja Dahl', 'Søren Frisk'])
        ->map(fn (string $name, int $id) => ['id' => $id + 1, 'label' => $name, 'group' => 'People']);
    $pages = collect(['Editor', 'Combobox', 'Kanban'])
        ->map(fn (string $title) => ['label' => $title, 'url' => url('/kit/'.mb_strtolower($title)), 'group' => 'Kit pages', 'description' => 'Kit']);

    return response()->json($people->concat($pages)
        ->filter(fn (array $item) => $query === '' || str_contains(mb_strtolower($item['label']), $query))
        ->values());
});
