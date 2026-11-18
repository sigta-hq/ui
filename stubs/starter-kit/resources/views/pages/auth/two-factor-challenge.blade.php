<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            class="relative flex h-auto w-full flex-col gap-6"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                focusOtp() {
                    this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                },
                init() {
                    if (! this.showRecoveryInput) {
                        this.focusOtp();
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : this.focusOtp();
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Authentication code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Recovery code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="flex flex-col gap-6">
                @csrf

                <div x-show="!showRecoveryInput" class="flex justify-center" x-ref="otp">
                    <sigta:otp
                        x-model="code"
                        length="6"
                        name="code"
                        :error="$errors->first('code')"
                    />
                </div>

                <div x-show="showRecoveryInput">
                    <sigta:input
                        type="text"
                        name="recovery_code"
                        x-ref="recovery_code"
                        x-bind:required="showRecoveryInput"
                        autocomplete="one-time-code"
                        x-model="recovery_code"
                        :aria-label="__('Recovery code')"
                        :error="$errors->first('recovery_code')"
                    />
                </div>

                <sigta:button variant="primary" type="submit" class="w-full justify-center">
                    {{ __('Continue') }}
                </sigta:button>
            </form>

            <p class="text-center font-mono text-[0.76rem] text-graphite">
                {{ __('or you can') }}
                <button type="button" x-show="!showRecoveryInput" x-on:click="toggleInput()" class="cursor-pointer text-ink underline decoration-line underline-offset-4 transition-colors duration-150 ease-out hover:decoration-ink">
                    {{ __('login using a recovery code') }}
                </button>
                <button type="button" x-show="showRecoveryInput" x-on:click="toggleInput()" class="cursor-pointer text-ink underline decoration-line underline-offset-4 transition-colors duration-150 ease-out hover:decoration-ink">
                    {{ __('login using an authentication code') }}
                </button>
            </p>
        </div>
    </div>
</x-layouts::auth>
