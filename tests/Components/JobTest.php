<?php

use Illuminate\Support\Facades\Blade;

it('renders a running job with its progress and stage', function () {
    $html = Blade::render('<x-sigta::job heading="Importing contacts" :value="62" stage="Matching duplicates" description="312 merged so far." />');

    expect($html)
        ->toContain('data-sigta="job"')
        ->toContain('x-data="sigtaJob"')
        ->toContain('data-state="working"')
        ->toContain('Importing contacts')
        ->toContain('62%')
        ->toContain('Running')
        ->toContain('Matching duplicates')
        ->toContain('312 merged so far.')
        ->toContain('--sigta-progress: 62')
        ->toContain('aria-label="Importing contacts"');
});

it('keeps the compass turning with no value', function () {
    $html = Blade::render('<x-sigta::job heading="Rebuilding the index" />');

    expect($html)
        ->not->toContain('%</span>')
        ->not->toContain('--sigta-progress')
        ->toContain('sigta-loader-frame');
});

it('names each state and colours the finished ones', function () {
    expect(Blade::render('<x-sigta::job heading="Export" state="queued" />'))->toContain('data-state="queued"')->toContain('Queued');
    expect(Blade::render('<x-sigta::job heading="Export" state="done" :value="80" />'))
        ->toContain('data-state="done"')
        ->toContain('text-success')
        ->toContain('100%');
    expect(Blade::render('<x-sigta::job heading="Export" state="failed" />'))->toContain('text-danger')->toContain('Failed');
    expect(Blade::render('<x-sigta::job heading="Export" state="lost" />'))->toContain('data-state="working"');
});

it('shows the elapsed clock only while the job waits or runs', function () {
    $since = now()->subMinute()->toIso8601String();

    expect(Blade::render('<x-sigta::job heading="Export" :since="$since" />', ['since' => $since]))->toContain('data-sigta="elapsed-clock"');
    expect(Blade::render('<x-sigta::job heading="Export" state="done" :since="$since" />', ['since' => $since]))->not->toContain('data-sigta="elapsed-clock"');
});

it('renders an actions slot and forwards attributes', function () {
    $html = Blade::render('<x-sigta::job heading="Sync" state="failed" wire:key="job-7"><x-slot:actions><button>Retry</button></x-slot:actions></x-sigta::job>');

    expect($html)
        ->toContain('wire:key="job-7"')
        ->toContain('<button>Retry</button>');
});
