{{-- Calendar: standalone Mon-first month grid. Shared engine behind the
     date-picker. Grid is server-rendered (42 stable cells) and hydrated by
     inline Alpine so month navigation is reactive and Livewire-morph-safe —
     the cell count never changes, state lives in Alpine, no DOM is mutated. --}}
@props([
    'name' => null,
    'value' => null,   // Y-m-d
    'min' => null,     // Y-m-d
    'max' => null,     // Y-m-d
])

@php
use Illuminate\Support\Carbon;

$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$weekdays = ['Mo' => 'Monday', 'Tu' => 'Tuesday', 'We' => 'Wednesday', 'Th' => 'Thursday', 'Fr' => 'Friday', 'Sa' => 'Saturday', 'Su' => 'Sunday'];

$today    = Carbon::today();
$selected = $value ? Carbon::parse($value)->startOfDay() : null;
$minDate  = $min ? Carbon::parse($min)->startOfDay() : null;
$maxDate  = $max ? Carbon::parse($max)->startOfDay() : null;

$isOut = fn (Carbon $d) => ($minDate && $d->lt($minDate)) || ($maxDate && $d->gt($maxDate));

$view      = ($selected ?: $today)->copy();
$viewYear  = (int) $view->year;
$viewMonth = (int) $view->month; // 1-12

// 42-cell (6×7) grid, Monday first. Leading/trailing days spill from adjacent months.
$first     = Carbon::create($viewYear, $viewMonth, 1)->startOfDay();
$offset    = ($first->dayOfWeek + 6) % 7; // Carbon dayOfWeek: 0=Sun → Mon-first offset
$gridStart = $first->copy()->subDays($offset);

$cells = [];
for ($i = 0; $i < 42; $i++) {
    $d = $gridStart->copy()->addDays($i);
    $cells[] = [
        'date'     => $d->format('Y-m-d'),
        'day'      => (int) $d->day,
        'outside'  => (int) $d->month !== $viewMonth,
        'disabled' => $isOut($d),
        'selected' => $selected && $d->isSameDay($selected),
        'today'    => $d->isSameDay($today),
        'label'    => $d->format('j F Y'),
    ];
}

// Initial roving-tabindex target: selected → today (in view) → first enabled → 1st.
$focus = null;
if ($selected && $selected->year === $viewYear && $selected->month === $viewMonth && ! $isOut($selected)) {
    $focus = $selected;
} elseif ($today->year === $viewYear && $today->month === $viewMonth && ! $isOut($today)) {
    $focus = $today;
} else {
    for ($d = 1; $d <= $first->daysInMonth; $d++) {
        $probe = Carbon::create($viewYear, $viewMonth, $d);
        if (! $isOut($probe)) { $focus = $probe; break; }
    }
}
$focusedIso = ($focus ?: $first)->format('Y-m-d');

$monthTitle = $monthNames[$viewMonth - 1].' '.$viewYear;
$canPrev = ! $minDate || $first->copy()->subDay()->gte($minDate);
$canNext = ! $maxDate || $first->copy()->addMonth()->lte($maxDate);

$cellClasses = function (array $c) {
    $cls = [];
    if ($c['selected']) $cls[] = 'bg-ink text-surface';
    if ($c['outside'] && ! $c['selected']) $cls[] = 'text-graphite/50';
    if (! $c['selected'] && ! $c['disabled']) $cls[] = 'hover:bg-ink/5';
    if ($c['today'] && ! $c['selected']) $cls[] = 'border border-ink/50';
    return implode(' ', $cls);
};

$base = 'relative flex items-center justify-center aspect-square w-full rounded-base font-mono text-[0.78rem] tabular-nums text-ink transition duration-150 ease-out focus:outline-none disabled:opacity-40 disabled:pointer-events-none';
$navBtn = 'inline-flex items-center justify-center size-8 rounded-base border border-transparent text-ink transition duration-150 ease-out hover:bg-ink/5 disabled:opacity-40 disabled:pointer-events-none';
@endphp

<div
    data-sigta="calendar"
    x-data="{
        viewYear: {{ $viewYear }},
        viewMonth: {{ $viewMonth }},
        selected: @js($selected?->format('Y-m-d')),
        focused: @js($focusedIso),
        min: @js($minDate?->format('Y-m-d')),
        max: @js($maxDate?->format('Y-m-d')),
        monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
        shortMonths: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        pad(n) { return String(n).padStart(2, '0'); },
        iso(y, m, d) { return y + '-' + this.pad(m) + '-' + this.pad(d); },
        todayIso() { const t = new Date(); return this.iso(t.getFullYear(), t.getMonth() + 1, t.getDate()); },
        get title() { return this.monthNames[this.viewMonth - 1] + ' ' + this.viewYear; },
        get canPrev() {
            if (! this.min) return true;
            const p = new Date(this.viewYear, this.viewMonth - 1, 0);
            return this.iso(p.getFullYear(), p.getMonth() + 1, p.getDate()) >= this.min;
        },
        get canNext() {
            if (! this.max) return true;
            const n = new Date(this.viewYear, this.viewMonth, 1);
            return this.iso(n.getFullYear(), n.getMonth() + 1, n.getDate()) <= this.max;
        },
        get cells() {
            const first = new Date(this.viewYear, this.viewMonth - 1, 1);
            const offset = (first.getDay() + 6) % 7;
            const start = new Date(this.viewYear, this.viewMonth - 1, 1 - offset);
            const today = this.todayIso();
            const out = [];
            for (let i = 0; i < 42; i++) {
                const d = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
                const iso = this.iso(d.getFullYear(), d.getMonth() + 1, d.getDate());
                out.push({
                    date: iso,
                    day: d.getDate(),
                    outside: (d.getMonth() + 1) !== this.viewMonth || d.getFullYear() !== this.viewYear,
                    disabled: this.isDisabled(iso),
                    selected: this.selected === iso,
                    today: today === iso,
                    label: d.getDate() + ' ' + this.monthNames[d.getMonth()] + ' ' + d.getFullYear(),
                });
            }
            return out;
        },
        isDisabled(iso) { return (this.min && iso < this.min) || (this.max && iso > this.max); },
        goToIso(iso) { const [y, m] = iso.split('-').map(Number); this.viewYear = y; this.viewMonth = m; },
        firstFocusable() {
            if (this.selected) {
                const [sy, sm] = this.selected.split('-').map(Number);
                if (sy === this.viewYear && sm === this.viewMonth && ! this.isDisabled(this.selected)) return this.selected;
            }
            const t = this.todayIso();
            const [ty, tm] = t.split('-').map(Number);
            if (ty === this.viewYear && tm === this.viewMonth && ! this.isDisabled(t)) return t;
            const dim = new Date(this.viewYear, this.viewMonth, 0).getDate();
            for (let d = 1; d <= dim; d++) {
                const iso = this.iso(this.viewYear, this.viewMonth, d);
                if (! this.isDisabled(iso)) return iso;
            }
            return this.iso(this.viewYear, this.viewMonth, 1);
        },
        prevMonth() { if (! this.canPrev) return; if (this.viewMonth === 1) { this.viewMonth = 12; this.viewYear--; } else { this.viewMonth--; } this.focused = this.firstFocusable(); },
        nextMonth() { if (! this.canNext) return; if (this.viewMonth === 12) { this.viewMonth = 1; this.viewYear++; } else { this.viewMonth++; } this.focused = this.firstFocusable(); },
        select(iso) {
            if (this.isDisabled(iso)) return;
            this.selected = iso;
            this.goToIso(iso);
            this.focused = iso;
            this.$nextTick(() => this.sync());
        },
        sync() {
            const el = this.$refs.value;
            if (el) {
                el.value = this.selected ?? '';
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
            this.$dispatch('calendar-change', { value: this.selected, label: this.selected ? this.formatLabel(this.selected) : '' });
        },
        formatLabel(iso) { const [y, m, d] = iso.split('-').map(Number); return d + ' ' + this.shortMonths[m - 1] + ' ' + y; },
        focusDate(iso) {
            this.focused = iso;
            this.goToIso(iso);
            this.$nextTick(() => { const b = this.$refs.grid.querySelector('[data-date=' + JSON.stringify(iso) + ']'); if (b) b.focus(); });
        },
        moveFocus(days) {
            const [y, m, d] = this.focused.split('-').map(Number);
            const nd = new Date(y, m - 1, d + days);
            const iso = this.iso(nd.getFullYear(), nd.getMonth() + 1, nd.getDate());
            if (this.isDisabled(iso)) return;
            this.focusDate(iso);
        },
        moveWeekEdge(end) {
            const [y, m, d] = this.focused.split('-').map(Number);
            const off = (new Date(y, m - 1, d).getDay() + 6) % 7;
            this.moveFocus(end ? 6 - off : -off);
        },
        moveMonth(delta) {
            const [y, m, d] = this.focused.split('-').map(Number);
            const nd = new Date(y, m - 1 + delta, d);
            const iso = this.iso(nd.getFullYear(), nd.getMonth() + 1, nd.getDate());
            if (this.isDisabled(iso)) return;
            this.focusDate(iso);
        },
    }"
    {{ $attributes->only('class')->merge(['class' => 'inline-flex flex-col gap-2 bg-surface-raised border border-line rounded-panel shadow-paper p-3 w-[17.5rem] select-none']) }}
>
    <div class="flex items-center justify-between">
        <button type="button" x-on:click="prevMonth()" :disabled="! canPrev" aria-label="Previous month" class="{{ $navBtn }}" @if (! $canPrev) disabled @endif>
            <x-sigta::icon name="chevron-left" size="sm" />
        </button>
        <div id="{{ $id }}-title" aria-live="polite" class="font-mono text-[0.72rem] tracking-[0.14em] uppercase text-ink" x-text="title">{{ $monthTitle }}</div>
        <button type="button" x-on:click="nextMonth()" :disabled="! canNext" aria-label="Next month" class="{{ $navBtn }}" @if (! $canNext) disabled @endif>
            <x-sigta::icon name="chevron-right" size="sm" />
        </button>
    </div>

    <div
        role="grid"
        aria-labelledby="{{ $id }}-title"
        x-ref="grid"
        x-on:keydown.left.prevent="moveFocus(-1)"
        x-on:keydown.right.prevent="moveFocus(1)"
        x-on:keydown.up.prevent="moveFocus(-7)"
        x-on:keydown.down.prevent="moveFocus(7)"
        x-on:keydown.home.prevent="moveWeekEdge(false)"
        x-on:keydown.end.prevent="moveWeekEdge(true)"
        x-on:keydown.page-up.prevent="moveMonth(-1)"
        x-on:keydown.page-down.prevent="moveMonth(1)"
    >
        <div role="row" class="grid grid-cols-7">
            @foreach ($weekdays as $abbr => $full)
                <span role="columnheader" aria-label="{{ $full }}" class="flex items-center justify-center py-1 font-mono text-[0.6rem] tracking-[0.1em] uppercase text-graphite">{{ $abbr }}</span>
            @endforeach
        </div>

        @for ($r = 0; $r < 6; $r++)
            <div role="row" class="grid grid-cols-7 gap-0.5">
                @for ($col = 0; $col < 7; $col++)
                    @php $i = $r * 7 + $col; $c = $cells[$i]; @endphp
                    <button
                        type="button"
                        role="gridcell"
                        data-date="{{ $c['date'] }}"
                        aria-label="{{ $c['label'] }}"
                        aria-selected="{{ $c['selected'] ? 'true' : 'false' }}"
                        tabindex="{{ $c['date'] === $focusedIso ? '0' : '-1' }}"
                        @if ($c['today']) aria-current="date" @endif
                        @if ($c['disabled']) disabled @endif
                        :data-date="cells[{{ $i }}].date"
                        x-text="cells[{{ $i }}].day"
                        :aria-label="cells[{{ $i }}].label"
                        :aria-selected="cells[{{ $i }}].selected ? 'true' : 'false'"
                        :aria-current="cells[{{ $i }}].today ? 'date' : false"
                        :tabindex="cells[{{ $i }}].date === focused ? '0' : '-1'"
                        :disabled="cells[{{ $i }}].disabled"
                        x-on:click="select(cells[{{ $i }}].date)"
                        class="{{ $base }} {{ $cellClasses($c) }}"
                        :class="{ 'bg-ink text-surface': cells[{{ $i }}].selected, 'text-graphite/50': cells[{{ $i }}].outside && ! cells[{{ $i }}].selected, 'hover:bg-ink/5': ! cells[{{ $i }}].selected && ! cells[{{ $i }}].disabled, 'border border-ink/50': cells[{{ $i }}].today && ! cells[{{ $i }}].selected }"
                    >{{ $c['day'] }}</button>
                @endfor
            </div>
        @endfor
    </div>

    <input type="hidden" x-ref="value" value="{{ $value }}" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }} />
</div>
