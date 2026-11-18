@php
    // Livewire re-renders this layout on every update, so compare against the
    // page's own URL rather than the update request's.
    $isCurrent = fn (string $route): bool => \Livewire\Livewire::originalUrl() === route($route);
@endphp

<div class="flex items-start gap-10 max-md:flex-col max-md:gap-6">
    <nav class="flex w-full flex-col gap-1 md:w-[220px] md:shrink-0" aria-label="{{ __('Settings') }}">
        <sigta:shell.link :href="route('profile.edit')" :current="$isCurrent('profile.edit')" wire:navigate>{{ __('Profile') }}</sigta:shell.link>

        @if (Route::has('security.edit'))
            <sigta:shell.link :href="route('security.edit')" :current="$isCurrent('security.edit')" wire:navigate>{{ __('Security') }}</sigta:shell.link>
        @endif

        @if (Route::has('appearance.edit'))
            <sigta:shell.link :href="route('appearance.edit')" :current="$isCurrent('appearance.edit')" wire:navigate>{{ __('Appearance') }}</sigta:shell.link>
        @endif
    </nav>

    <sigta:separator class="w-full md:hidden" />

    <div class="flex-1 self-stretch">
        <sigta:heading level="2" size="base">{{ $heading ?? '' }}</sigta:heading>
        <sigta:text variant="muted">{{ $subheading ?? '' }}</sigta:text>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
