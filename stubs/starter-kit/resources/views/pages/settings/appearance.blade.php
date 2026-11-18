<section class="w-full">
    @include('partials.settings-heading')

    <sigta:heading level="2" class="sr-only">{{ __('Appearance settings') }}</sigta:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <sigta:callout>
            {{ __('This app has one look, set by its theme. Change the theme in resources/css/app.css.') }}
        </sigta:callout>
    </x-pages::settings.layout>
</section>
