{{-- Calendar: standalone Mon-first month grid. Shared engine behind the
     date-picker. Grid is server-rendered (42 stable cells) and hydrated by the
     sigtaCalendar Alpine component (resources/js/sigta.js) so month navigation
     is reactive and Livewire-morph-safe — the cell count never changes, state
     lives in Alpine, no DOM is mutated. The date math matches the core's
     (packages/core/src/calendar.ts), which the Svelte twin renders with.
     Dates are ISO (Y-m-d) strings or Carbon/DateTimeInterface values. --}}
@props([
    'name' => null,
    'value' => null,   // Y-m-d
    'min' => null,     // Y-m-d
    'max' => null,     // Y-m-d
    'today' => null,   // Y-m-d; null = the current date (the browser's, once Alpine runs)
])

@php
use Illuminate\Support\Carbon;

$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$weekdays = ['Mo' => 'Monday', 'Tu' => 'Tuesday', 'We' => 'Wednesday', 'Th' => 'Thursday', 'Fr' => 'Friday', 'Sa' => 'Saturday', 'Su' => 'Sunday'];

$day = fn ($date) => ($date === null || $date === '') ? null : Carbon::parse($date)->startOfDay();

$pinnedToday = $day($today);
$todayDate = $pinnedToday ?? Carbon::today();
$selected = $day($value);
$minDate  = $day($min);
$maxDate  = $day($max);

$isOut = fn (Carbon $d) => ($minDate && $d->lt($minDate)) || ($maxDate && $d->gt($maxDate));

$view      = ($selected ?: $todayDate)->copy();
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
        'disabled' => $isOut($d),
        'selected' => $selected && $d->isSameDay($selected),
        'today'    => $d->isSameDay($todayDate),
        'label'    => $d->format('j F Y'),
    ];
}

// Initial roving-tabindex target: selected → today (in view) → first enabled → 1st.
$focus = null;
if ($selected && $selected->year === $viewYear && $selected->month === $viewMonth && ! $isOut($selected)) {
    $focus = $selected;
} elseif ($todayDate->year === $viewYear && $todayDate->month === $viewMonth && ! $isOut($todayDate)) {
    $focus = $todayDate;
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

$config = [
    'value' => $selected?->format('Y-m-d'),
    'min' => $minDate?->format('Y-m-d'),
    'max' => $maxDate?->format('Y-m-d'),
    'today' => $pinnedToday?->format('Y-m-d'),
    'year' => $viewYear,
    'month' => $viewMonth,
    'focused' => $focusedIso,
];
@endphp

<div
    data-sigta="calendar"
    x-data="sigtaCalendar(@js($config))"
    {{ $attributes->only('class') }}
>
    <div data-scope="calendar" data-part="header">
        <button type="button" x-on:click="prevMonth()" x-bind:disabled="! canPrev" aria-label="Previous month" data-scope="calendar" data-part="prev" @if (! $canPrev) disabled @endif>
            <x-sigta::icon name="chevron-left" size="sm" />
        </button>
        <div id="{{ $id }}-title" aria-live="polite" data-scope="calendar" data-part="title" x-text="title">{{ $monthTitle }}</div>
        <button type="button" x-on:click="nextMonth()" x-bind:disabled="! canNext" aria-label="Next month" data-scope="calendar" data-part="next" @if (! $canNext) disabled @endif>
            <x-sigta::icon name="chevron-right" size="sm" />
        </button>
    </div>

    <div
        role="grid"
        aria-labelledby="{{ $id }}-title"
        x-ref="grid"
        x-on:keydown="key($event)"
        data-scope="calendar"
        data-part="grid"
    >
        <div role="row" data-scope="calendar" data-part="weekdays">
            @foreach ($weekdays as $abbr => $full)
                <span role="columnheader" aria-label="{{ $full }}" data-scope="calendar" data-part="weekday">{{ $abbr }}</span>
            @endforeach
        </div>

        @for ($r = 0; $r < 6; $r++)
            <div role="row" data-scope="calendar" data-part="week">
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
                        data-scope="calendar"
                        data-part="day"
                        x-bind:data-date="cells[{{ $i }}].date"
                        x-text="cells[{{ $i }}].day"
                        x-bind:aria-label="cells[{{ $i }}].label"
                        x-bind:aria-selected="cells[{{ $i }}].selected ? 'true' : 'false'"
                        x-bind:aria-current="cells[{{ $i }}].today ? 'date' : null"
                        x-bind:tabindex="cells[{{ $i }}].date === focused ? '0' : '-1'"
                        x-bind:disabled="cells[{{ $i }}].disabled"
                        x-on:click="select(cells[{{ $i }}].date)"
                    >{{ $c['day'] }}</button>
                @endfor
            </div>
        @endfor
    </div>

    <input type="hidden" x-ref="value" value="{{ $config['value'] }}" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }} />
</div>
