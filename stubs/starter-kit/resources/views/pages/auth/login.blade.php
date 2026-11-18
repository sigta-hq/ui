<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status :status="session('status')" />

        {{-- @feature:passkeys --}}
        <x-passkey-verify />
        {{-- @endfeature:passkeys --}}

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
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
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <sigta:input
                    name="password"
                    :label="__('Password')"
                    :error="$errors->first('password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                />

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="absolute end-0 top-0 font-mono text-[0.7rem] text-ink underline decoration-line underline-offset-4 transition-colors duration-150 ease-out hover:decoration-ink" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Remember Me -->
            <sigta:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <sigta:button variant="primary" type="submit" class="w-full justify-center" data-test="login-button">
                {{ __('Log in') }}
            </sigta:button>
        </form>

        {{-- @feature:registration --}}
        <p class="text-center font-mono text-[0.76rem] text-graphite">
            {{ __('Don\'t have an account?') }}
            <a href="{{ route('register') }}" class="text-ink underline decoration-line underline-offset-4 transition-colors duration-150 ease-out hover:decoration-ink" wire:navigate>{{ __('Sign up') }}</a>
        </p>
        {{-- @endfeature:registration --}}
    </div>
</x-layouts::auth>
