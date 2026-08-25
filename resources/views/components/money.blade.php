@props([
    'minor' => null,        // amount in MINOR units (øre / cents)
    'currency' => 'DKK',    // ISO code; drives symbol, position, and separators
    'decimals' => 2,        // fraction digits to show (0 for whole-major display)
    'symbol' => null,       // override the mapped symbol
    'position' => null,     // 'before' | 'after' — override the mapped position
    'thousands' => null,    // override the mapped thousands separator
    'decimal' => null,      // override the mapped decimal separator
    'suffix' => null,       // muted per-unit suffix, e.g. "/ hour"
])

{{--
    Format a minor-unit amount (øre / cents) for display. The amount and the
    currency stay app data — this only renders them in the brand's tabular
    numerals. Known currencies carry sensible symbol/position/separators;
    override any piece, or pass an unknown code (rendered as a trailing symbol).

        <sigta:money :minor="$rate->amount_minor" :currency="$rate->currency->value" />
        <sigta:money :minor="145000" currency="DKK" suffix="/ hour" />
        <sigta:money :minor="1234" currency="USD" :decimals="0" />
--}}
@php
$known = [
    'DKK' => ['symbol' => 'kr.', 'position' => 'after',  'thousands' => '.', 'decimal' => ','],
    'EUR' => ['symbol' => '€',   'position' => 'before', 'thousands' => '.', 'decimal' => ','],
    'USD' => ['symbol' => '$',   'position' => 'before', 'thousands' => ',', 'decimal' => '.'],
    'GBP' => ['symbol' => '£',   'position' => 'before', 'thousands' => ',', 'decimal' => '.'],
    'SEK' => ['symbol' => 'kr',  'position' => 'after',  'thousands' => ' ', 'decimal' => ','],
    'NOK' => ['symbol' => 'kr',  'position' => 'after',  'thousands' => ' ', 'decimal' => ','],
];

$code = strtoupper((string) $currency);
$meta = $known[$code] ?? ['symbol' => $code, 'position' => 'after', 'thousands' => ',', 'decimal' => '.'];

$sym = $symbol ?? $meta['symbol'];
$pos = $position ?? $meta['position'];
$thou = $thousands ?? $meta['thousands'];
$dec = $decimal ?? $meta['decimal'];

$digits = max(0, (int) $decimals);
$formatted = number_format(($minor ?? 0) / 100, $digits, $dec, $thou);

$amount = $pos === 'before' ? $sym.$formatted : $formatted.' '.$sym;
@endphp

<span data-sigta="money" data-currency="{{ $code }}" {{ $attributes->merge(['class' => 'tabular-nums']) }}>{{ $amount }}@if ($suffix)<span class="text-graphite"> {{ $suffix }}</span>@endif</span>
