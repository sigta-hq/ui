@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        getDefaultPasskeyName() {
            const ua = navigator.userAgent;

            const browser = [
                { pattern: /Edg|Edge/, name: 'Edge' },
                { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
                { pattern: /Firefox|FxiOS/, name: 'Firefox' },
                { pattern: /Chrome|CriOS/, name: 'Chrome' },
                { pattern: /Safari/, name: 'Safari' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            const os = [
                { pattern: /iPhone/, name: 'iPhone' },
                { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
                { pattern: /Android/, name: 'Android' },
                { pattern: /Mac/, name: 'Mac' },
                { pattern: /Windows/, name: 'Windows' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            return [browser, os].filter(Boolean).join(' on ') || '';
        },
        init() {
            this.name = this.getDefaultPasskeyName();
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name });
                this.name = '';
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
        cancel() {
            this.showForm = false;
            this.name = '';
            this.error = null;
        },
    }"
>
    <template x-if="!supported">
        <sigta:text variant="muted">{{ __('Passkeys are not supported in this browser.') }}</sigta:text>
    </template>

    <template x-if="supported && !showForm">
        <div>
            <sigta:button variant="primary" x-on:click="showForm = true">
                <sigta:icon name="plus" size="sm" />
                {{ __('Add passkey') }}
            </sigta:button>
        </div>
    </template>

    <template x-if="supported && showForm">
        <div class="flex flex-col gap-4 rounded-panel border border-line bg-surface p-4">
            <sigta:input
                :label="__('Passkey name')"
                :description="__('Give this passkey a name to help you identify it later.')"
                x-model="name"
                :placeholder="__('e.g., MacBook Pro, iPhone')"
                x-on:keydown.enter.prevent="register()"
                x-ref="passkeyNameInput"
                x-init="$nextTick(() => $refs.passkeyNameInput?.focus())"
            />

            <p x-show="error" x-text="error" x-cloak class="font-mono text-[0.7rem] leading-relaxed text-danger"></p>

            <div class="flex gap-2">
                <sigta:button
                    variant="primary"
                    x-on:click="register()"
                    x-bind:disabled="loading || !name.trim()"
                    x-bind:data-loading="loading"
                >
                    {{ __('Register passkey') }}
                </sigta:button>
                <sigta:button variant="ghost" x-on:click="cancel()">
                    {{ __('Cancel') }}
                </sigta:button>
            </div>
        </div>
    </template>
</div>
