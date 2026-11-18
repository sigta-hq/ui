<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

        <!-- Session Status -->
        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <sigta:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                :error="$errors->first('email')"
                type="email"
                required
                autofocus
                placeholder="email@example.com"
            />

            <sigta:button variant="primary" type="submit" class="w-full justify-center" data-test="email-password-reset-link-button">
                {{ __('Email password reset link') }}
            </sigta:button>
        </form>

        <p class="text-center font-mono text-[0.76rem] text-graphite">
            {{ __('Or, return to') }}
            <a href="{{ route('login') }}" class="text-ink underline decoration-line underline-offset-4 transition-colors duration-150 ease-out hover:decoration-ink" wire:navigate>{{ __('log in') }}</a>
        </p>
    </div>
</x-layouts::auth>
