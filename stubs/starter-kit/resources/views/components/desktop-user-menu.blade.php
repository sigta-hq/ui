@props([
    'name' => null,
    'position' => 'top-start',
])

@php
    $user = auth()->user();
    $name ??= $user->name;
@endphp

<sigta:dropdown :position="$position" {{ $attributes }}>
    <x-slot:trigger>
        <button
            type="button"
            class="flex w-full min-w-0 cursor-pointer items-center gap-2.5 rounded-base px-2 py-1.5 text-start transition-colors duration-150 ease-out hover:bg-ink/5"
            data-test="sidebar-menu-button"
        >
            <sigta:avatar :name="$name" size="sm" />
            <span class="min-w-0 flex-1 truncate font-mono text-[0.76rem] text-ink">{{ $name }}</span>
            <sigta:icon name="chevrons-up-down" size="sm" class="shrink-0 text-graphite" />
        </button>
    </x-slot:trigger>

    {{-- A set width lets the menu grow past a narrow trigger; long addresses truncate. --}}
    <div class="w-64 max-w-[calc(100vw-2rem)] px-3.5 py-2">
        <sigta:profile :name="$name" :meta="$user->email" class="w-full [&_span]:truncate" />
    </div>

    <sigta:dropdown.separator />

    <sigta:dropdown.item icon="settings" :href="route('profile.edit')" wire:navigate>
        {{ __('Settings') }}
    </sigta:dropdown.item>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <sigta:dropdown.item type="submit" icon="log-out" data-test="logout-button">
            {{ __('Log out') }}
        </sigta:dropdown.item>
    </form>
</sigta:dropdown>
