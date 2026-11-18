<x-layouts::auth :title="__('Email verification')">
    <div class="mt-4 flex flex-col gap-6">
        <sigta:text class="text-center">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </sigta:text>

        @if (session('status') == 'verification-link-sent')
            <sigta:callout variant="success">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </sigta:callout>
        @endif

        <div class="flex flex-col items-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <sigta:button variant="primary" type="submit" class="w-full justify-center">
                    {{ __('Resend verification email') }}
                </sigta:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <sigta:button variant="ghost" type="submit" size="sm" data-test="logout-button">
                    {{ __('Log out') }}
                </sigta:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
