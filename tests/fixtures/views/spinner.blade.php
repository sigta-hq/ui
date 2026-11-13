{{-- A consumer's loader view, written as an anonymous component would be. --}}
@props(['state' => 'working', 'value' => null, 'size' => 'base', 'label' => null])
<svg {{ $attributes->merge(['class' => 'group-data-[state=working]/loader:animate-spin']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true" data-spinner data-seen-state="{{ $state }}" data-seen-value="{{ $value }}" data-seen-size="{{ $size }}" data-seen-label="{{ $label }}"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" /></svg>
