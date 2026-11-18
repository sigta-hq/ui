<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Welcome')])
    </head>
    <body class="min-h-screen antialiased">
        <sigta:paper />

        <div class="relative z-10 flex min-h-screen flex-col">
            <sigta:navbar>
                <x-slot:brand>
                    <x-app-logo :href="route('home')" />
                </x-slot:brand>

                <sigta:navlink href="https://laravel.com/docs" target="_blank" rel="noopener">{{ __('Documentation') }}</sigta:navlink>
                <sigta:navlink href="https://laracasts.com" target="_blank" rel="noopener">{{ __('Laracasts') }}</sigta:navlink>

                @if (Route::has('login'))
                    <x-slot:actions>
                        @auth
                            <sigta:button variant="primary" size="sm" :href="route('dashboard')">
                                {{ __('Dashboard') }}
                            </sigta:button>
                        @else
                            <sigta:button variant="ghost" size="sm" :href="route('login')">
                                {{ __('Log in') }}
                            </sigta:button>

                            {{-- @feature:registration --}}
                            @if (Route::has('register'))
                                <sigta:button variant="primary" size="sm" :href="route('register')">
                                    {{ __('Register') }}
                                </sigta:button>
                            @endif
                            {{-- @endfeature:registration --}}
                        @endauth
                    </x-slot:actions>
                @endif
            </sigta:navbar>

            <main class="flex flex-1 items-center">
                <sigta:section.hero
                    :eyebrow="config('app.name', 'Laravel')"
                    :heading="__('Let\'s get started')"
                    :lede="__('Laravel has an incredibly rich ecosystem. We suggest starting with the following.')"
                    :primary-label="__('Read the documentation')"
                    primary-href="https://laravel.com/docs"
                    target="_blank"
                    :secondary-label="__('Watch video tutorials')"
                    secondary-href="https://laracasts.com"
                    secondary-target="_blank"
                    class="w-full"
                />
            </main>
        </div>

        @livewireScripts
    </body>
</html>
