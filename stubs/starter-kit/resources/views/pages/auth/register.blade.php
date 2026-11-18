<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Name -->
            <sigta:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                :error="$errors->first('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <sigta:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                :error="$errors->first('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <sigta:input
                name="password"
                :label="__('Password')"
                :error="$errors->first('password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            />

            <!-- Confirm Password -->
            <sigta:input
                name="password_confirmation"
                :label="__('Confirm password')"
                :error="$errors->first('password_confirmation')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            />

            <sigta:button variant="primary" type="submit" class="w-full justify-center" data-test="register-user-button">
                {{ __('Create account') }}
            </sigta:button>
        </form>

        <p class="text-center font-mono text-[0.76rem] text-graphite">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-ink underline decoration-line underline-offset-4 transition-colors duration-150 ease-out hover:decoration-ink" wire:navigate>{{ __('Log in') }}</a>
        </p>
    </div>
</x-layouts::auth>
