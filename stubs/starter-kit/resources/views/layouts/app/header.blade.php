<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased">
        <sigta:navbar>
            <x-slot:brand>
                <x-app-logo :href="route('dashboard')" wire:navigate />
            </x-slot:brand>

            <sigta:navlink :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </sigta:navlink>

            <sigta:navlink href="https://github.com/laravel/livewire-starter-kit" target="_blank" rel="noopener">
                {{ __('Repository') }}
            </sigta:navlink>

            <sigta:navlink href="https://laravel.com/docs/starter-kits#livewire" target="_blank" rel="noopener">
                {{ __('Documentation') }}
            </sigta:navlink>

            <x-slot:actions>
                <x-desktop-user-menu position="bottom-end" />
            </x-slot:actions>
        </sigta:navbar>

        <div class="mx-auto w-full max-w-(--sigta-maxw) px-[calc(var(--sigta-gutter)-1.5rem)]">
            {{ $slot }}
        </div>

        @persist('toast')
            <sigta:toast />
        @endpersist

        @livewireScripts
    </body>
</html>
