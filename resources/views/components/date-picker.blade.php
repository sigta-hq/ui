{{-- Date picker: brand text input + calendar-icon button that opens the shared
     <x-sigta::calendar> in an anchored popover. Open/close reuses sigtaDropdown
     (escape / click-outside / focus return); an outer inline scope holds the
     display text and the Y-m-d model. Selecting a day fills both; the input also
     accepts typed dates. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'name' => null,
    'value' => null,       // Y-m-d
    'min' => null,         // Y-m-d
    'max' => null,         // Y-m-d
    'placeholder' => 'Select a date',
    'variant' => 'default',
])

@php
use Illuminate\Support\Carbon;

$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$panelId = $id.'-panel';
$displayValue = $value ? Carbon::parse($value)->format('j M Y') : '';
$describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''));
$disabled = $attributes->has('disabled');

$inline = $variant === 'inline';

// Inline reads as a value until it is hovered or focused, so the calendar button
// waits too; its frame sits in the margin so the date lines up with plain values.
$inputClasses = match (true) {
    $inline => 'peer w-full font-body text-[0.98rem] text-ink bg-transparent placeholder:text-graphite/70 border-[1.5px] rounded-[5px] px-[0.8em] py-[0.3em] pr-[2.6em] transition duration-150 ease-out hover:border-line focus:outline-none focus:border-ink disabled:opacity-40 disabled:pointer-events-none',
    default => 'w-full font-body text-[0.98rem] text-ink bg-surface placeholder:text-graphite/70 border-[1.5px] rounded-[5px] px-[0.8em] py-[0.62em] pr-[2.6em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none',
};
$inputClasses .= $error ? ' border-danger' : ($inline ? ' border-transparent' : ' border-line');
$buttonClasses = 'absolute inset-y-0 right-0 flex items-center px-[0.75em] text-graphite hover:text-ink transition duration-150 ease-out disabled:opacity-40 disabled:pointer-events-none'
    .($inline ? ' opacity-0 group-hover/date:opacity-100 peer-focus:opacity-100 focus-visible:opacity-100' : '');
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="date-picker"
        x-data="{
            display: @js($displayValue),
            value: @js($value),
            shortMonths: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            format(iso) { if (! iso) return ''; const [y, m, d] = iso.split('-').map(Number); return d + ' ' + this.shortMonths[m - 1] + ' ' + y; },
            parse(text) {
                const t = String(text).trim();
                if (! t) return '';
                const ts = Date.parse(t);
                if (isNaN(ts)) return null;
                const dt = new Date(ts);
                const p = (n) => String(n).padStart(2, '0');
                return dt.getFullYear() + '-' + p(dt.getMonth() + 1) + '-' + p(dt.getDate());
            },
        }"
        data-variant="{{ $variant }}"
        {{ $attributes->only('class')->merge(['class' => $inline ? 'relative -mx-[0.8em]' : 'relative']) }}
    >
        <div
            x-data="sigtaDropdown"
            x-on:keydown.escape.prevent.stop="close(true)"
            x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
            x-on:calendar-change="value = $event.detail.value; display = $event.detail.label; $refs.hidden.value = value ?? ''; $refs.hidden.dispatchEvent(new Event('input', { bubbles: true })); $refs.hidden.dispatchEvent(new Event('change', { bubbles: true })); close(true)"
            class="relative"
        >
            <div x-ref="trigger" class="{{ $inline ? 'group/date relative' : 'relative' }}">
                <input
                    type="text"
                    id="{{ $id }}"
                    role="combobox"
                    aria-haspopup="dialog"
                    aria-controls="{{ $panelId }}"
                    x-bind:aria-expanded="open"
                    x-model="display"
                    x-on:change="const iso = parse(display); if (iso === null) { display = format(value); } else { value = iso; display = format(iso); } $refs.hidden.value = value ?? ''; $refs.hidden.dispatchEvent(new Event('input', { bubbles: true })); $refs.hidden.dispatchEvent(new Event('change', { bubbles: true }))"
                    x-on:keydown.enter.prevent="$event.target.blur()"
                    x-on:keydown.down.prevent="open ? null : toggle()"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                    class="{{ $inputClasses }}"
                    @if ($disabled) disabled @endif
                    @if ($error) aria-invalid="true" @endif
                    @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                />
                <button type="button" tabindex="-1" x-on:click="toggle()" aria-label="Open calendar" @if ($disabled) disabled @endif class="{{ $buttonClasses }}">
                    <x-sigta::icon name="calendar-days" size="sm" />
                </button>
            </div>

            <input type="hidden" x-ref="hidden" value="{{ $value }}" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }} />

            <div
                x-cloak
                x-show="open"
                x-on:click.outside="close()"
                id="{{ $panelId }}"
                role="dialog"
                aria-label="Choose date"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute top-full left-0 mt-2 z-50"
            >
                <x-sigta::calendar :value="$value" :min="$min" :max="$max" />
            </div>
        </div>
    </div>
</x-sigta::field>
