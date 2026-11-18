<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Confirm password')"
            :description="__('This is a secure area of the application. Please confirm your password before continuing.')"
        />

        <x-auth-session-status :status="session('status')" />

        {{-- @feature:passkeys --}}
        <x-passkey-verify
            options-route="passkey.confirm-options"
            submit-route="passkey.confirm"
            :label="__('Confirm with passkey')"
            :loading-label="__('Confirming...')"
            :separator="__('Or confirm with password')"
        />
        {{-- @endfeature:passkeys --}}

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <sigta:input
                name="password"
                :label="__('Password')"
                :error="$errors->first('password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
            />

            <sigta:button variant="primary" type="submit" class="w-full justify-center" data-test="confirm-password-button">
                {{ __('Confirm') }}
            </sigta:button>
        </form>
    </div>
</x-layouts::auth>
