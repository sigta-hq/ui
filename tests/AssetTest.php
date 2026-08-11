<?php

it('serves the runtime script over the asset route', function () {
    $this->get('/sigta/sigta.js')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/javascript; charset=utf-8');
});

it('serves the editor bundle over its asset route', function () {
    $this->get('/sigta/editor.js')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/javascript; charset=utf-8');
});

it('emits a cache-busted editor script tag from @sigtaEditorScripts', function () {
    $html = Illuminate\Support\Facades\Blade::render('@sigtaEditorScripts');

    expect($html)->toContain('/sigta/editor.js?v=');
});

it('emits a cache-busted script tag from @sigtaScripts', function () {
    $html = Illuminate\Support\Facades\Blade::render('@sigtaScripts');

    expect($html)
        ->toContain('/sigta/sigta.js?v=')
        ->toContain('defer');
});
