<div
    class="flex flex-col gap-6 rounded-panel border border-line bg-surface-raised py-6"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="flex flex-col gap-2 px-6">
        <div class="flex items-center gap-2">
            <sigta:icon name="lock" size="sm" />
            <sigta:heading level="3" size="base">{{ __('2FA recovery codes') }}</sigta:heading>
        </div>
        <sigta:text variant="muted">
            {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
        </sigta:text>
    </div>

    <div class="px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <sigta:button
                x-show="!showRecoveryCodes"
                variant="primary"
                wire:click="viewRecoveryCodes"
                x-on:click="showRecoveryCodes = true"
                aria-expanded="false"
                aria-controls="recovery-codes-section"
            >
                <sigta:icon name="eye" size="sm" />
                {{ __('View recovery codes') }}
            </sigta:button>

            <sigta:button
                x-show="showRecoveryCodes"
                x-cloak
                variant="primary"
                x-on:click="showRecoveryCodes = false"
                aria-expanded="true"
                aria-controls="recovery-codes-section"
            >
                <sigta:icon name="eye-off" size="sm" />
                {{ __('Hide recovery codes') }}
            </sigta:button>

            @if (filled($recoveryCodes))
                <sigta:button x-show="showRecoveryCodes" x-cloak wire:click="regenerateRecoveryCodes">
                    <sigta:icon name="refresh-cw" size="sm" />
                    {{ __('Regenerate codes') }}
                </sigta:button>
            @endif
        </div>

        <div
            x-show="showRecoveryCodes"
            x-cloak
            x-transition
            id="recovery-codes-section"
            class="relative overflow-hidden"
            x-bind:aria-hidden="!showRecoveryCodes"
        >
            <div class="mt-3 flex flex-col gap-3">
                @error('recoveryCodes')
                    <sigta:callout variant="danger" :heading="$message" />
                @enderror

                @if (filled($recoveryCodes))
                    <div
                        class="grid gap-1 rounded-base border border-line bg-surface p-4 font-mono text-[0.82rem] tabular-nums"
                        role="list"
                        aria-label="{{ __('Recovery codes') }}"
                    >
                        @foreach ($recoveryCodes as $code)
                            <div role="listitem" class="select-text transition-opacity" wire:loading.class="opacity-50">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                    <sigta:text variant="caption">
                        {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate codes above.') }}
                    </sigta:text>
                @endif
            </div>
        </div>
    </div>
</div>
