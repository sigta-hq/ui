<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased">
        <sigta:paper />

        <div class="relative z-10 grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="hidden h-full flex-col bg-inverse p-10 text-inverse-fg lg:flex">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-inverse-fg no-underline" wire:navigate>
                    <x-app-logo-icon class="size-7" />
                    <span class="font-head text-[1.3rem] font-semibold tracking-[0.02em]">{{ config('app.name', 'Laravel') }}</span>
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <figure class="mt-auto flex flex-col gap-3">
                    <blockquote>
                        <sigta:heading level="2" size="lg" class="text-inverse-fg">&ldquo;{{ trim($message) }}&rdquo;</sigta:heading>
                    </blockquote>
                    <figcaption class="font-mono text-[0.72rem] uppercase tracking-[0.14em] text-inverse-fg-muted">{{ trim($author) }}</figcaption>
                </figure>
            </div>

            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center gap-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 text-ink no-underline lg:hidden" wire:navigate>
                        <x-app-logo-icon class="size-9" />
                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <sigta:toast />
        @endpersist

        @livewireScripts
    </body>
</html>
