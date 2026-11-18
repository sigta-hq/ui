<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <!-- Session Status -->
        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <sigta:input
                name="email"
                value="{{ old('email', request('email')) }}"
                :label="__('Email')"
                :error="$errors->first('email')"
                type="email"
                required
                autocomplete="email"
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

            <sigta:button variant="primary" type="submit" class="w-full justify-center" data-test="reset-password-button">
                {{ __('Reset password') }}
            </sigta:button>
        </form>
    </div>
</x-layouts::auth>
