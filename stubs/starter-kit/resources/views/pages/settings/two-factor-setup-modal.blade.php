<sigta:modal name="two-factor-setup-modal" x-on:close="$wire.closeModal()">
    <div class="flex flex-col gap-6">
        <div class="flex flex-col items-center gap-4">
            <div class="flex size-14 items-center justify-center rounded-full border-[1.5px] border-ink bg-surface text-ink">
                <sigta:icon name="qr-code" size="lg" />
            </div>

            <div class="flex flex-col gap-2 text-center">
                <sigta:heading level="2" size="lg">{{ $this->modalConfig['title'] }}</sigta:heading>
                <sigta:text variant="muted">{{ $this->modalConfig['description'] }}</sigta:text>
            </div>
        </div>

        @if ($showVerificationStep)
            <div class="flex flex-col gap-6">
                <div
                    class="flex flex-col items-center justify-center gap-3"
                    x-data
                    x-init="$nextTick(() => $el.querySelector('input')?.focus())"
                >
                    <sigta:otp name="code" wire:model="code" length="6" :error="$errors->first('code')" />
                </div>

                <div class="flex items-center gap-3">
                    <sigta:button class="flex-1 justify-center" wire:click="resetVerification">
                        {{ __('Back') }}
                    </sigta:button>

                    <sigta:button
                        variant="primary"
                        class="flex-1 justify-center"
                        wire:click="confirmTwoFactor"
                        x-bind:disabled="$wire.code.length < 6"
                    >
                        {{ __('Confirm') }}
                    </sigta:button>
                </div>
            </div>
        @else
            @error('setupData')
                <sigta:callout variant="danger" :heading="$message" />
            @enderror

            <div class="flex justify-center">
                {{-- The code takes this square whether it has arrived or not, so nothing shifts when it does. --}}
                <div class="relative flex aspect-square w-64 items-center justify-center overflow-hidden rounded-panel border border-line bg-surface-raised p-4">
                    @empty($qrCodeSvg)
                        <sigta:loader size="lg" :label="__('Preparing your code')" />
                    @else
                        <div class="[&_svg]:h-auto [&_svg]:w-full">
                            {!! $qrCodeSvg !!}
                        </div>
                    @endempty
                </div>
            </div>

            <sigta:button
                variant="primary"
                class="w-full justify-center"
                :disabled="$errors->has('setupData')"
                wire:click="showVerificationIfNecessary"
            >
                {{ $this->modalConfig['buttonText'] }}
            </sigta:button>

            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-3 font-mono text-[0.66rem] uppercase tracking-[0.14em] text-graphite">
                    <span class="h-px flex-1 bg-line"></span>
                    {{ __('or, enter the code manually') }}
                    <span class="h-px flex-1 bg-line"></span>
                </div>

                <div
                    class="flex items-stretch gap-2"
                    x-data="{
                        copied: false,
                        async copy() {
                            try {
                                await navigator.clipboard.writeText(@js($manualSetupKey));
                                this.copied = true;
                                setTimeout(() => this.copied = false, 1500);
                            } catch (e) {
                                console.warn('Could not copy to clipboard');
                            }
                        }
                    }"
                >
                    @empty($manualSetupKey)
                        <div class="flex w-full items-center justify-center rounded-[5px] border-[1.5px] border-line bg-surface p-3">
                            <sigta:loader size="sm" :label="__('Preparing your key')" />
                        </div>
                    @else
                        <div class="flex-1">
                            <sigta:input type="text" readonly :value="$manualSetupKey" class="font-mono" :aria-label="__('Setup key')" />
                        </div>

                        <sigta:button variant="ghost" x-on:click="copy()" :aria-label="__('Copy setup key')">
                            <span x-show="!copied"><sigta:icon name="copy" size="sm" /></span>
                            <span x-show="copied" x-cloak class="text-success"><sigta:icon name="check" size="sm" /></span>
                        </sigta:button>
                    @endempty
                </div>
            </div>
        @endif
    </div>
</sigta:modal>
