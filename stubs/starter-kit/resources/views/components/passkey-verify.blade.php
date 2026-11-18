@props([
    'optionsRoute' => 'passkey.login-options',
    'submitRoute' => 'passkey.login',
    'label' => __('Sign in with a passkey'),
    'loadingLabel' => __('Authenticating...'),
    'separator' => __('Or continue with email'),
])

@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async verify() {
            this.loading = true;
            this.error = null;
            try {
                const response = await window.Passkeys.verify({
                    routes: {
                        options: '{{ route($optionsRoute) }}',
                        submit: '{{ route($submitRoute) }}',
                    },
                });
                Livewire.navigate(response.redirect || '/dashboard');
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
    }"
>
    <template x-if="supported">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <sigta:button
                    class="w-full justify-center"
                    x-on:click="verify()"
                    x-bind:disabled="loading"
                    x-bind:data-loading="loading"
                >
                    <sigta:icon name="fingerprint" size="sm" />
                    {{ $label }}
                </sigta:button>
                <p x-show="error" x-text="error" x-cloak class="text-center font-mono text-[0.7rem] leading-relaxed text-danger"></p>
            </div>

            <div class="flex items-center gap-3 font-mono text-[0.66rem] uppercase tracking-[0.14em] text-graphite">
                <span class="h-px flex-1 bg-line"></span>
                {{ $separator }}
                <span class="h-px flex-1 bg-line"></span>
            </div>
        </div>
    </template>
</div>
