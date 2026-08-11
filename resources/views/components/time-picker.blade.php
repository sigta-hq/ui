{{-- Time picker: brand text input + clock-icon button that opens a listbox of
     times (every `step` minutes, bounded by min/max). Self-contained inline
     Alpine mirrors the dropdown open/close pattern and adds listbox roving;
     value forwards through a hidden input as "HH:MM". `format` (24|12) controls
     the visible labels only — the model stays 24h. Typed times are accepted. --}}
@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'name' => null,
    'value' => null,       // HH:MM (24h)
    'min' => null,         // HH:MM
    'max' => null,         // HH:MM
    'step' => 30,          // minutes
    'format' => '24',      // '24' | '12'
    'placeholder' => 'Select a time',
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);
$listId = $id.'-listbox';
$describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''));
$disabled = $attributes->has('disabled');

$step   = max(1, (int) $step);
$toMin  = fn ($t) => (int) explode(':', $t)[0] * 60 + (int) explode(':', $t)[1];
$startM = $min ? $toMin($min) : 0;
$endM   = $max ? $toMin($max) : 23 * 60 + 59;

$fmt = function (int $h, int $m) use ($format) {
    if ($format === '12') {
        $ap = $h < 12 ? 'AM' : 'PM';
        $hh = $h % 12 ?: 12;
        return $hh.':'.sprintf('%02d', $m).' '.$ap;
    }
    return sprintf('%02d:%02d', $h, $m);
};

$options = [];
for ($mm = $startM; $mm <= $endM; $mm += $step) {
    $h = intdiv($mm, 60);
    $m = $mm % 60;
    $val = sprintf('%02d:%02d', $h, $m);
    $options[] = ['value' => $val, 'label' => $fmt($h, $m), 'selected' => $val === $value];
}

$displayValue = $value ? $fmt((int) explode(':', $value)[0], (int) explode(':', $value)[1]) : '';

$inputClasses = 'w-full font-body text-[0.98rem] text-ink bg-surface placeholder:text-graphite/70 border-[1.5px] rounded-[5px] px-[0.8em] py-[0.62em] pr-[2.6em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none';
$inputClasses .= $error ? ' border-danger' : ' border-line';
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <div
        data-sigta="time-picker"
        x-data="{
            open: false,
            value: @js($value),
            display: @js($displayValue),
            fmt12: @js($format === '12'),
            opts: @js(array_map(fn ($o) => ['value' => $o['value'], 'label' => $o['label']], $options)),
            activeIndex: -1,
            toggle() { this.open ? this.close() : this.openList(); },
            openList() { this.open = true; this.$nextTick(() => this.syncActive()); },
            close(focus = false) { this.open = false; this.activeIndex = -1; if (focus) this.$refs.input.focus(); },
            syncActive() { const i = this.opts.findIndex((o) => o.value === this.value); this.setActive(i >= 0 ? i : 0); },
            setActive(i) {
                if (! this.opts.length) return;
                this.activeIndex = ((i % this.opts.length) + this.opts.length) % this.opts.length;
                this.$nextTick(() => { const el = this.$refs.list?.children[this.activeIndex]; if (el) el.scrollIntoView({ block: 'nearest' }); });
            },
            move(delta) { if (! this.open) { this.openList(); return; } this.setActive(this.activeIndex < 0 ? 0 : this.activeIndex + delta); },
            chooseActive() { const o = this.opts[this.activeIndex]; if (o) this.choose(o.value, o.label); },
            choose(val, label) {
                this.value = val;
                this.display = label;
                this.$refs.hidden.value = val;
                this.$refs.hidden.dispatchEvent(new Event('input', { bubbles: true }));
                this.$refs.hidden.dispatchEvent(new Event('change', { bubbles: true }));
                this.close(true);
            },
            format(val) {
                if (! val) return '';
                const [h, m] = val.split(':').map(Number);
                if (this.fmt12) { const ap = h < 12 ? 'AM' : 'PM'; const hh = h % 12 || 12; return hh + ':' + String(m).padStart(2, '0') + ' ' + ap; }
                return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
            },
            parseTyped() {
                const match = String(this.display).trim().match(/^(\d{1,2}):(\d{2})\s*(am|pm)?$/i);
                if (match) {
                    let h = +match[1]; const mn = +match[2]; const ap = match[3] ? match[3].toLowerCase() : null;
                    if (ap) { if (h === 12) h = 0; if (ap === 'pm') h += 12; }
                    if (h >= 0 && h < 24 && mn >= 0 && mn < 60) {
                        const val = String(h).padStart(2, '0') + ':' + String(mn).padStart(2, '0');
                        this.value = val;
                        this.display = this.format(val);
                        this.$refs.hidden.value = val;
                        this.$refs.hidden.dispatchEvent(new Event('input', { bubbles: true }));
                        this.$refs.hidden.dispatchEvent(new Event('change', { bubbles: true }));
                        return;
                    }
                }
                this.display = this.format(this.value);
            },
        }"
        x-on:keydown.escape.prevent.stop="close(true)"
        x-on:focusout="if (! $el.contains($event.relatedTarget)) close()"
        {{ $attributes->only('class')->merge(['class' => 'relative']) }}
    >
        <div class="relative">
            <input
                type="text"
                x-ref="input"
                id="{{ $id }}"
                role="combobox"
                aria-haspopup="listbox"
                aria-controls="{{ $listId }}"
                x-bind:aria-expanded="open"
                x-model="display"
                x-on:click="openList()"
                x-on:change="parseTyped()"
                x-on:keydown.down.prevent="move(1)"
                x-on:keydown.up.prevent="move(-1)"
                x-on:keydown.enter.prevent="open ? chooseActive() : openList()"
                placeholder="{{ $placeholder }}"
                autocomplete="off"
                class="{{ $inputClasses }}"
                @if ($disabled) disabled @endif
                @if ($error) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            />
            <button type="button" tabindex="-1" x-on:click="toggle()" aria-label="Open time list" @if ($disabled) disabled @endif class="absolute inset-y-0 right-0 flex items-center px-[0.75em] text-graphite hover:text-ink transition duration-150 ease-out disabled:opacity-40 disabled:pointer-events-none">
                <x-sigta::icon name="clock" size="sm" />
            </button>
        </div>

        <input type="hidden" x-ref="hidden" value="{{ $value }}" @if ($name) name="{{ $name }}" @endif {{ $attributes->except(['class', 'id']) }} />

        <ul
            x-cloak
            x-show="open"
            x-ref="list"
            id="{{ $listId }}"
            role="listbox"
            aria-label="{{ $label ?? 'Times' }}"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="absolute top-full left-0 mt-2 z-50 min-w-40 max-h-60 overflow-y-auto bg-surface-raised border border-line rounded-panel shadow-paper py-1.5"
        >
            @foreach ($options as $i => $opt)
                <li role="presentation">
                    <button
                        type="button"
                        role="option"
                        data-value="{{ $opt['value'] }}"
                        aria-selected="{{ $opt['selected'] ? 'true' : 'false' }}"
                        :aria-selected="value === @js($opt['value']) ? 'true' : 'false'"
                        :data-active="activeIndex === {{ $i }}"
                        tabindex="-1"
                        x-on:click="choose(@js($opt['value']), @js($opt['label']))"
                        x-on:mousemove="activeIndex = {{ $i }}"
                        class="block w-full text-left px-3.5 py-1.5 font-mono text-[0.78rem] tabular-nums cursor-pointer transition duration-150 ease-out {{ $opt['selected'] ? 'bg-ink text-surface' : 'text-ink' }}"
                        :class="{ 'bg-ink text-surface': value === @js($opt['value']), 'bg-ink/5': activeIndex === {{ $i }} && value !== @js($opt['value']) }"
                    >{{ $opt['label'] }}</button>
                </li>
            @endforeach
        </ul>
    </div>
</x-sigta::field>
