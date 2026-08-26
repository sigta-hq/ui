@props([
    'minutes' => null,
    'format' => 'compact',
])

{{--
    Static duration display. Whole minutes rendered as a compact "1h 15m" label
    (default) or an "HH:MM" clock. Exact hours drop the minutes ("2h"); zero
    renders as "0m". For a value that counts up live use <sigta:elapsed-clock>.

        <sigta:duration :minutes="$task->estimate_minutes" />
        <sigta:duration :minutes="90" format="clock" />   (renders 01:30)
--}}
@php
$total = max(0, (int) ($minutes ?? 0));
$h = intdiv($total, 60);
$m = $total % 60;

$text = match ($format) {
    'clock' => sprintf('%02d:%02d', $h, $m),
    default => $h === 0 ? $m.'m' : ($m === 0 ? $h.'h' : $h.'h '.$m.'m'),
};
@endphp

<span data-sigta="duration" {{ $attributes->merge(['class' => 'tabular-nums']) }}>{{ $text }}</span>
