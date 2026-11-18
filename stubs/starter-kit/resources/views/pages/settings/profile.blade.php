<section class="w-full">
    @include('partials.settings-heading')

    <sigta:heading level="2" class="sr-only">{{ __('Profile settings') }}</sigta:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 flex w-full flex-col gap-6">
            <sigta:input wire:model="name" :label="__('Name')" :error="$errors->first('name')" type="text" required autofocus autocomplete="name" />

            <div class="flex flex-col gap-4">
                <sigta:input wire:model="email" :label="__('Email')" :error="$errors->first('email')" type="email" required autocomplete="email" />

                {{-- @feature:email-verification --}}
                @if ($this->hasUnverifiedEmail)
                    <div class="flex flex-col gap-3">
                        <sigta:text>
                            {{ __('Your email address is unverified.') }}

                            <button type="button" wire:click.prevent="resendVerificationNotification" class="cursor-pointer text-ink underline decoration-line underline-offset-4 transition-colors duration-150 ease-out hover:decoration-ink">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </sigta:text>

                        @if (session('status') === 'verification-link-sent')
                            <sigta:callout variant="success">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </sigta:callout>
                        @endif
                    </div>
                @endif
                {{-- @endfeature:email-verification --}}
            </div>

            <div class="flex items-center gap-4">
                <sigta:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Save') }}
                </sigta:button>
            </div>
        </form>

        {{-- @feature:email-verification --}}
        @if ($this->showDeleteUser)
        {{-- @endfeature:email-verification --}}
            <livewire:pages::settings.delete-user-form />
        {{-- @feature:email-verification --}}
        @endif
        {{-- @endfeature:email-verification --}}
    </x-pages::settings.layout>
</section>
