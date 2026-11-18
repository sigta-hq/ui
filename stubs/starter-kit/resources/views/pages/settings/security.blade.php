<section class="w-full">
    @include('partials.settings-heading')

    <sigta:heading level="2" class="sr-only">{{ __('Security settings') }}</sigta:heading>

    <x-pages::settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 flex flex-col gap-6">
            <sigta:input
                wire:model="current_password"
                :label="__('Current password')"
                :error="$errors->first('current_password')"
                type="password"
                required
                autocomplete="current-password"
            />
            <sigta:input
                wire:model="password"
                :label="__('New password')"
                :error="$errors->first('password')"
                type="password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            />
            <sigta:input
                wire:model="password_confirmation"
                :label="__('Confirm password')"
                :error="$errors->first('password_confirmation')"
                type="password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            />

            <div class="flex items-center gap-4">
                <sigta:button variant="primary" type="submit" data-test="update-password-button">
                    {{ __('Save') }}
                </sigta:button>
            </div>
        </form>

        {{-- @feature:2fa --}}
        @if ($canManageTwoFactor)
            <section class="mt-12 flex flex-col gap-1">
                <sigta:heading level="2" size="base">{{ __('Two-factor authentication') }}</sigta:heading>
                <sigta:text variant="muted">{{ __('Manage your two-factor authentication settings') }}</sigta:text>

                <div class="mt-5 flex w-full flex-col gap-6" wire:cloak>
                    @if ($twoFactorEnabled)
                        <div class="flex flex-col gap-4">
                            <sigta:text>
                                {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                            </sigta:text>

                            <div>
                                <sigta:button variant="danger" wire:click="disable">
                                    {{ __('Disable 2FA') }}
                                </sigta:button>
                            </div>

                            <livewire:pages::settings.two-factor.recovery-codes :$requiresConfirmation />
                        </div>
                    @else
                        <div class="flex flex-col gap-4">
                            <sigta:text variant="muted">
                                {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                            </sigta:text>

                            <div>
                                <sigta:modal.trigger name="two-factor-setup-modal">
                                    <sigta:button variant="primary" wire:click="$dispatch('start-two-factor-setup')">
                                        {{ __('Enable 2FA') }}
                                    </sigta:button>
                                </sigta:modal.trigger>
                            </div>

                            <livewire:pages::settings.two-factor-setup-modal :requires-confirmation="$requiresConfirmation" />
                        </div>
                    @endif
                </div>
            </section>
        @endif
        {{-- @endfeature:2fa --}}

        {{-- @feature:passkeys --}}
        @if ($canManagePasskeys)
            <section class="mt-12 flex flex-col gap-1">
                <sigta:heading level="2" size="base">{{ __('Passkeys') }}</sigta:heading>
                <sigta:text variant="muted">{{ __('Manage your passkeys for passwordless sign-in') }}</sigta:text>

                <div class="mt-5 flex w-full flex-col gap-6" wire:cloak>
                    <div class="overflow-hidden rounded-panel border border-line bg-surface-raised">
                        @forelse ($passkeys as $passkey)
                            <div class="flex items-center justify-between gap-4 p-4 {{ ! $loop->last ? 'border-b border-line' : '' }}" wire:key="passkey-{{ $passkey['id'] }}">
                                <div class="flex min-w-0 items-center gap-4">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-panel border border-line bg-surface text-graphite">
                                        <sigta:icon name="key-round" />
                                    </div>
                                    <div class="flex min-w-0 flex-col gap-1">
                                        <div class="flex items-center gap-2.5">
                                            <span class="truncate font-head font-medium">{{ $passkey['name'] }}</span>
                                            @if ($passkey['authenticator'])
                                                <sigta:badge>{{ $passkey['authenticator'] }}</sigta:badge>
                                            @endif
                                        </div>
                                        <span class="font-mono text-[0.68rem] tracking-[0.04em] text-graphite">
                                            {{ __('Added :time', ['time' => $passkey['created_at_diff']]) }}
                                            @if ($passkey['last_used_at_diff'])
                                                <span class="mx-1 opacity-50">/</span>
                                                {{ __('Last used :time', ['time' => $passkey['last_used_at_diff']]) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <sigta:tooltip :text="__('Remove passkey')">
                                    <sigta:button
                                        variant="ghost"
                                        size="sm"
                                        class="text-danger"
                                        wire:click="confirmDelete({{ $passkey['id'] }})"
                                        :aria-label="__('Remove passkey')"
                                    >
                                        <sigta:icon name="trash-2" size="sm" />
                                    </sigta:button>
                                </sigta:tooltip>
                            </div>
                        @empty
                            <div class="flex flex-col items-center gap-1 p-8 text-center">
                                <div class="mb-3 flex size-14 items-center justify-center rounded-panel border border-line bg-surface text-graphite">
                                    <sigta:icon name="key-round" size="lg" />
                                </div>
                                <span class="font-head font-medium">{{ __('No passkeys yet') }}</span>
                                <sigta:text variant="muted">{{ __('Add a passkey to sign in without a password') }}</sigta:text>
                            </div>
                        @endforelse
                    </div>

                    <x-passkey-registration />
                </div>
            </section>
        @endif
        {{-- @endfeature:passkeys --}}
    </x-pages::settings.layout>

    {{-- @feature:passkeys --}}
    {{-- The open state follows $showDeleteModal both ways: the server opens it in
         confirmDelete(), and closing it here (Escape, backdrop, Cancel) resets it. --}}
    <sigta:modal
        name="delete-passkey-modal"
        size="sm"
        :heading="__('Remove passkey')"
        x-effect="$wire.showDeleteModal ? show() : hide()"
        x-on:close="$wire.showDeleteModal && $wire.closeDeleteModal()"
    >
        <div class="flex flex-col gap-6">
            <sigta:text variant="muted">
                {{ __('Are you sure you want to remove the passkey ":name"? You will no longer be able to use it to sign in.', ['name' => $deletingPasskeyName]) }}
            </sigta:text>

            <div class="flex justify-end gap-2">
                <sigta:button variant="ghost" wire:click="closeDeleteModal">
                    {{ __('Cancel') }}
                </sigta:button>
                <sigta:button variant="danger" wire:click="deletePasskey">
                    {{ __('Remove passkey') }}
                </sigta:button>
            </div>
        </div>
    </sigta:modal>
    {{-- @endfeature:passkeys --}}
</section>
