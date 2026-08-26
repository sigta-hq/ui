@props([
    'since' => null,
])

{{--
    Live-ticking elapsed clock (HH:MM:SS) counting up from a start instant.
    Pass an ISO-8601 string or a Carbon/DateTimeInterface via :since; omit for
    "now". Purely presentational — which entry is running and how it stops live
    in your app; this only renders the counting value.

        <sigta:elapsed-clock :since="$entry->started_at" class="text-lg" />
--}}
@php
$startIso = $since instanceof \DateTimeInterface ? $since->format(\DateTime::ATOM) : $since;
@endphp

<span
    data-sigta="elapsed-clock"
    {{ $attributes->merge(['class' => 'tabular-nums']) }}
    x-data="{
        started: {{ $startIso ? "new Date('".$startIso."').getTime()" : 'Date.now()' }},
        now: Date.now(),
        get label() {
            const secs = Math.max(0, Math.floor((this.now - this.started) / 1000));
            const h = String(Math.floor(secs / 3600)).padStart(2, '0');
            const m = String(Math.floor((secs % 3600) / 60)).padStart(2, '0');
            const s = String(secs % 60).padStart(2, '0');
            return `${h}:${m}:${s}`;
        },
    }"
    x-init="const t = setInterval(() => now = Date.now(), 1000); $el._clockTimer = t"
    x-on:destroy="clearInterval($el._clockTimer)"
    x-text="label"
>00:00:00</span>
