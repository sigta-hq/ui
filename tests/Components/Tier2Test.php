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

it('reads the neutral paginator shape both runtimes accept', function () {
    $html = Blade::render('<x-sigta::pagination :paginator="[\'currentPage\' => 3, \'lastPage\' => 12]" url="/invoices?page=" />');

    expect($html)
        ->toContain('href="/invoices?page=2"')
        ->toContain('href="/invoices?page=4"')
        ->toContain('aria-label="Page 12"')
        ->toMatch('/aria-current="page"[^>]*>3</');

    // A simple paginator (no last page): prev / next only.
    $simple = Blade::render('<x-sigta::pagination :paginator="[\'currentPage\' => 2, \'hasMorePages\' => false]" url="?page=%d" />');
    expect($simple)->toContain('href="?page=1"')->not->toContain('aria-label="Next page"')->not->toContain('aria-label="Page');

    // A paginator's toArray() reads too.
    $paginator = new LengthAwarePaginator(range(1, 10), 50, 10, 2, ['path' => '/things']);
    $array = Blade::render('<x-sigta::pagination :paginator="$p" url="?page=%d" />', ['p' => $paginator->toArray()]);
    expect($array)->toContain('href="?page=3"')->toContain('aria-label="Page 5"');
});

it('takes a closure for the page url', function () {
    $html = Blade::render('<x-sigta::pagination :pages="3" :current="2" :url="fn ($page) => \'/p/\'.$page" />');

    expect($html)->toContain('href="/p/1"')->toContain('href="/p/3"');
});

it('marks pages and arrows with contract parts', function () {
    $html = Blade::render('<x-sigta::pagination :pages="10" :current="5" />');

    expect($html)
        ->toContain('data-scope="pagination" data-part="list"')
        ->toContain('data-part="prev"')
        ->toContain('data-part="next"')
        ->toContain('data-part="gap"')
        ->toContain('<button type="button" aria-label="Page 4"')
        ->not->toContain('class=');

    expect(contractCss('pagination'))
        ->toMatch('/&\[aria-current="page"\] \{\s*@apply bg-ink text-surface;/')
        ->toContain('aria-disabled:opacity-40');
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
    expect($html)->toContain('role="progressbar"')->not->toContain('aria-valuenow')->toContain('data-indeterminate=""');

    expect(contractCss('progress'))->toContain('@keyframes sigta-progress-sweep')->toContain('animate-[sigta-progress-sweep_1.4s_ease-in-out_infinite]');
});

it('fills the progress bar through --sigta-progress', function () {
    expect(Blade::render('<x-sigta::progress :value="30" :max="120" />'))
        ->toContain('data-scope="progress" data-part="fill" style="--sigta-progress: 25"')
        ->not->toContain('data-indeterminate');

    expect(contractCss('progress'))->toContain('width: calc(var(--sigta-progress, 0) * 1%);');
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

    expect($html)->toContain('data-scope="slider" data-part="value"');
    expect(contractCss('slider'))->toContain('[&::-webkit-slider-thumb]:border-danger');
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
        ->toContain('<ol data-sigta="timeline"')
        ->toContain('data-sigta="timeline.item"')
        ->toContain('Kickoff')
        ->toContain('Shipped')
        ->toContain('data-variant="accent"')
        ->toContain('<span aria-hidden="true" data-scope="timeline.item" data-part="dot"></span>');

    // accent variant dot
    expect(contractCss('timeline'))->toContain('[data-sigta="timeline.item"][data-variant="accent"] > & { @apply border border-ink bg-accent; }');
});

it('spaces timeline parts only when something sits above them', function () {
    expect(Blade::render('<x-sigta::timeline.item heading="Solo" />'))
        ->toContain('<h3 data-scope="timeline.item" data-part="heading">Solo</h3>')
        ->not->toContain('data-part="meta"')
        ->not->toContain('data-part="body"');

    expect(contractCss('timeline'))
        ->toContain('[data-scope="timeline.item"][data-part="meta"] + & { @apply mt-1; }')
        ->toContain('[data-scope="timeline.item"]:is([data-part="meta"], [data-part="heading"]) + & { @apply mt-1.5; }');
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
    expect(preg_match_all('/data-scope="otp"\s+data-part="box"/', $html))->toBe(4);
});

it('points the otp boxes at the field description and error it renders', function () {
    $html = Blade::render('<x-sigta::otp id="code" :length="2" description="From your email." error="Expired." />');

    expect($html)
        ->toContain('id="code-0-description"')
        ->toContain('id="code-0-error"')
        ->toContain('aria-describedby="code-0-description code-0-error"');

    expect(contractCss('otp'))->toContain('&[aria-invalid="true"] {')->toContain('@apply border-danger focus:border-ink;');
});

/* -------- file upload ----------------------------------------------------- */

it('renders a file dropzone forwarding wire:model onto the native input', function () {
    $html = Blade::render('<x-sigta::file-upload label="Attachment" wire:model="file" accept="image/*" />');

    expect($html)
        ->toContain('data-sigta="file-upload"')
        ->toContain('x-data="sigtaFileUpload"')
        ->toContain('type="file"')
        ->toContain('wire:model="file"')
        ->toContain('accept="image/*"')
        ->toContain('Attachment')
        ->toContain('data-scope="file-upload"'."\n".'                data-part="input"')
        ->not->toContain('class=');

    // The native input is visually hidden inside its dashed label zone.
    expect(contractCss('file-upload'))
        ->toMatch('/\[data-part="input"\] \{\s*@apply sr-only;/')
        ->toContain('border-[1.5px] border-dashed border-line')
        ->toContain('data-[dragging]:border-ink');
});

it('supports multiple files and error state', function () {
    $html = Blade::render('<x-sigta::file-upload multiple error="Too big" />');
    expect($html)->toContain('multiple')->toContain('Too big')->toContain('data-invalid')->toContain('aria-invalid="true"');

    // Red, but hover and a drag still ink it.
    expect(contractCss('file-upload'))->toContain('&:where([data-invalid]) { @apply border-danger; }');
});

it('dims a disabled file upload zone', function () {
    expect(Blade::render('<x-sigta::file-upload disabled />'))->toContain('data-disabled')->toContain('disabled');
    expect(Blade::render('<x-sigta::file-upload />'))->not->toContain('data-disabled')->not->toContain('data-invalid');

    expect(contractCss('file-upload'))->toContain('&[data-disabled] { @apply opacity-40 pointer-events-none; }');
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
        ->toContain('data-scope="shell.link" data-part="icon"')
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
        ->toContain('Right-click me')
        ->toContain('data-scope="context-menu"')
        ->toContain('style="left:0px;top:0px"')
        ->not->toContain('class=');

    expect(contractCss('context-menu'))->toContain('@apply fixed z-50 min-w-48');
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

    expect(contractCss('listbox'))->toContain('&[aria-invalid="true"] {')->toContain('@apply border-danger focus:border-ink;');
});

it('renders the listbox with contract parts', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::listbox label="Plan" value="studio" placeholder="Pick…" class="max-w-xs" id="plan">
            <x-sigta::autocomplete.option value="studio">Studio</x-sigta::autocomplete.option>
        </x-sigta::listbox>
    BLADE);

    expect($html)
        ->not->toMatch('/class="(?!max-w-xs)/')
        ->toMatch('/class="max-w-xs"[^>]*data-sigta="listbox"/s')         // class on the root
        ->toMatch('/data-part="trigger"[^>]*id="plan"/s')                  // id on the trigger
        ->toMatch('/data-empty=""[^>]*data-part="value"\s*>Pick…<\/span>/s')   // placeholder until Alpine starts
        ->toContain('x-bind:data-empty="value ? null : \'\'"')
        ->toContain('data-part="chevron"')
        ->toMatch('/aria-expanded="false"\s+x-bind:aria-expanded="open"/')
        ->toMatch('/<input\s+type="hidden"[^>]*value="studio"/s');

    expect(contractCss('listbox'))
        ->toContain('[aria-expanded="true"] > &')
        ->toContain('@apply rotate-180;')
        ->toContain('@apply font-mono text-[0.78rem] text-graphite;');
});
