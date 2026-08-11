<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Blade;

/* -------- pagination ------------------------------------------------------ */

it('renders manual pagination with a windowed page list', function () {
    $html = Blade::render('<x-sigta::pagination :pages="10" :current="5" url="?page=%d" />');

    expect($html)
        ->toContain('data-sigta="pagination"')
        ->toContain('aria-label="Pagination"')
        ->toContain('aria-current="page"')
        ->toContain('href="?page=4"')  // prev
        ->toContain('href="?page=6"')  // next
        ->toContain('…');              // ellipsis for 10 pages
});

it('disables prev at the first page and next at the last', function () {
    $first = Blade::render('<x-sigta::pagination :pages="5" :current="1" url="?page=%d" />');
    expect($first)->toContain('aria-label="Next page"')
        ->and($first)->not->toContain('aria-label="Previous page"');

    $last = Blade::render('<x-sigta::pagination :pages="5" :current="5" url="?page=%d" />');
    expect($last)->toContain('aria-label="Previous page"')
        ->and($last)->not->toContain('aria-label="Next page"');
});

it('drives pagination from a Laravel paginator', function () {
    $paginator = new LengthAwarePaginator(range(1, 10), 50, 10, 3, ['path' => '/things']);
    $html = Blade::render('<x-sigta::pagination :paginator="$paginator" />', compact('paginator'));

    expect($html)->toContain('/things?page=2')->toContain('/things?page=4')->toContain('aria-current="page"');
});

/* -------- progress -------------------------------------------------------- */

it('renders a determinate progressbar with aria values', function () {
    $html = Blade::render('<x-sigta::progress :value="30" :max="120" label="Uploading" />');

    expect($html)
        ->toContain('role="progressbar"')
        ->toContain('aria-valuenow="30"')
        ->toContain('aria-valuemax="120"')
        ->toContain('aria-valuemin="0"')
        ->toContain('25%')          // 30/120
        ->toContain('Uploading');
});

it('omits aria-valuenow when indeterminate', function () {
    $html = Blade::render('<x-sigta::progress indeterminate />');
    expect($html)->toContain('role="progressbar"')->not->toContain('aria-valuenow');
});

/* -------- slider ---------------------------------------------------------- */

it('renders a range slider that forwards wire:model and errors', function () {
    $html = Blade::render('<x-sigta::slider label="Volume" min="0" max="10" wire:model="volume" show-value />');

    expect($html)
        ->toContain('type="range"')
        ->toContain('wire:model="volume"')
        ->toContain('<output')
        ->toContain('Volume');

    expect(Blade::render('<x-sigta::slider label="V" error="Too loud" />'))
        ->toContain('aria-invalid="true"')->toContain('Too loud');
});

/* -------- timeline -------------------------------------------------------- */

it('renders a timeline as an ordered list of items', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::timeline>
            <x-sigta::timeline.item heading="Kickoff" meta="Jan">Scoped the work.</x-sigta::timeline.item>
            <x-sigta::timeline.item heading="Shipped" meta="Mar" variant="accent">Live for all.</x-sigta::timeline.item>
        </x-sigta::timeline>
    BLADE);

    expect($html)
        ->toContain('data-sigta="timeline.item"')
        ->toContain('Kickoff')
        ->toContain('Shipped')
        ->toContain('bg-accent');   // accent variant dot
});

/* -------- otp ------------------------------------------------------------- */

it('renders an otp field of N boxes over one hidden input', function () {
    $html = Blade::render('<x-sigta::otp :length="4" name="code" wire:model="code" />');

    expect($html)
        ->toContain('data-sigta="otp"')
        ->toContain('inputmode="numeric"')
        ->toContain('autocomplete="one-time-code"')
        ->toContain('aria-label="Digit 1 of 4"')
        ->toContain('aria-label="Digit 4 of 4"')
        ->toContain('type="hidden"')
        ->toContain('wire:model="code"');

    expect(substr_count($html, 'inputmode="numeric"'))->toBe(4);
});

/* -------- file upload ----------------------------------------------------- */

it('renders a file dropzone forwarding wire:model onto the native input', function () {
    $html = Blade::render('<x-sigta::file-upload label="Attachment" wire:model="file" accept="image/*" />');

    expect($html)
        ->toContain('data-sigta="file-upload"')
        ->toContain('type="file"')
        ->toContain('wire:model="file"')
        ->toContain('accept="image/*"')
        ->toContain('Attachment');
});

it('supports multiple files and error state', function () {
    $html = Blade::render('<x-sigta::file-upload multiple error="Too big" />');
    expect($html)->toContain('multiple')->toContain('Too big')->toContain('border-danger');
});

/* -------- charts ---------------------------------------------------------- */

it('renders all three chart types', function () {
    expect(Blade::render('<x-sigta::chart.sparkline :values="[1,4,2,8,5]" label="Trend" />'))
        ->toContain('data-sigta="chart.sparkline"')->toContain('role="img"')->toContain('aria-label="Trend"');
    expect(Blade::render('<x-sigta::chart.line :values="[1,4,2,8,5]" :labels="[\'W1\',\'W5\']" />'))
        ->toContain('data-sigta="chart.line"')->toContain('linearGradient');
    expect(Blade::render('<x-sigta::chart.bars :values="[1,4,2,8,5]" :highlight="3" trend />'))
        ->toContain('data-sigta="chart.bars"')->toContain('var(--sigta-accent)');
});

it('survives degenerate chart data without dividing by zero', function () {
    foreach (['[]', '[5]', '[3,3,3]'] as $data) {
        expect(Blade::render("<x-sigta::chart.sparkline :values=\"{$data}\" />"))->toContain('<svg');
        expect(Blade::render("<x-sigta::chart.line :values=\"{$data}\" />"))->toContain('<svg');
        expect(Blade::render("<x-sigta::chart.bars :values=\"{$data}\" />"))->toContain('<svg');
    }
});

it('keeps charts free of raw hex (currentColor / tokens only)', function () {
    $html = Blade::render('<x-sigta::chart.line :values="[1,2,3]" /><x-sigta::chart.bars :values="[1,2,3]" :highlight="1" /><x-sigta::chart.sparkline :values="[1,2,3]" />');
    expect($html)->not->toMatch('/#[0-9a-fA-F]{6}/');
});

/* -------- app shell ------------------------------------------------------- */

it('renders the app shell with a current nav link', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::shell>
            <x-slot:sidebar>
                <x-sigta::shell.sidebar>
                    <x-sigta::shell.link icon="house" current>Overview</x-sigta::shell.link>
                    <x-sigta::shell.link icon="chart-line">Trends</x-sigta::shell.link>
                </x-sigta::shell.sidebar>
            </x-slot:sidebar>
            <x-sigta::shell.main>
                <x-sigta::shell.bar heading="Overview" sub="Q2 2026" />
                Body
            </x-sigta::shell.main>
        </x-sigta::shell>
    BLADE);

    expect($html)
        ->toContain('data-sigta="shell"')
        ->toContain('data-sigta="shell.bar"')
        ->toContain('aria-current="page"')
        ->toContain('Overview')
        ->toContain('Q2 2026');
});

/* -------- context menu ---------------------------------------------------- */

it('renders a context menu with contextmenu handler and menu items', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::context-menu>
            <div>Right-click me</div>
            <x-slot:menu>
                <x-sigta::dropdown.item icon="pencil">Rename</x-sigta::dropdown.item>
            </x-slot:menu>
        </x-sigta::context-menu>
    BLADE);

    expect($html)
        ->toContain('data-sigta="context-menu"')
        ->toContain('contextmenu.prevent')
        ->toContain('role="menu"')
        ->toContain('role="menuitem"')
        ->toContain('Right-click me');
});

/* -------- listbox --------------------------------------------------------- */

it('renders a rich listbox with combobox semantics and hidden value input', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::listbox label="Country" name="country" wire:model="country" placeholder="Pick…">
            <x-sigta::autocomplete.option value="dk">Denmark</x-sigta::autocomplete.option>
            <x-sigta::autocomplete.option value="se">Sweden</x-sigta::autocomplete.option>
        </x-sigta::listbox>
    BLADE);

    expect($html)
        ->toContain('data-sigta="listbox"')
        ->toContain('role="combobox"')
        ->toContain('aria-haspopup="listbox"')
        ->toContain('role="listbox"')
        ->toContain('type="hidden"')
        ->toContain('wire:model="country"')
        ->toContain('Country');
});

it('shows a listbox error state', function () {
    expect(Blade::render('<x-sigta::listbox label="C" error="Required" />'))
        ->toContain('aria-invalid="true"')->toContain('Required');
});
