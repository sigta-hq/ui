<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased">
        <sigta:shell full>
            <x-slot:sidebar>
                <sigta:shell.sidebar :aria-label="__('Platform')" class="md:sticky md:top-0 md:z-20 md:h-screen">
                    <x-slot:brand>
                        <x-app-logo :href="route('dashboard')" wire:navigate />
                    </x-slot:brand>

                    <p class="px-[0.65em] pb-1 font-mono text-[0.62rem] uppercase tracking-[0.14em] text-graphite">{{ __('Platform') }}</p>

                    <sigta:shell.link icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </sigta:shell.link>

                    <div class="mt-auto flex flex-col gap-1 pt-6">
                        <sigta:shell.link icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank" rel="noopener">
                            {{ __('Repository') }}
                        </sigta:shell.link>

                        <sigta:shell.link icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank" rel="noopener">
                            {{ __('Documentation') }}
                        </sigta:shell.link>

                        <sigta:separator class="my-2" />

                        <x-desktop-user-menu class="w-full" />
                    </div>
                </sigta:shell.sidebar>
            </x-slot:sidebar>

            {{ $slot }}
        </sigta:shell>

        @persist('toast')
            <sigta:toast />
        @endpersist

        @livewireScripts
    </body>
</html>
