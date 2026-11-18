<section class="mt-10 flex flex-col gap-6">
    <div class="relative flex flex-col gap-1">
        <sigta:heading level="2" size="base">{{ __('Delete account') }}</sigta:heading>
        <sigta:text variant="muted">{{ __('Delete your account and all of its resources') }}</sigta:text>
    </div>

    <div>
        <sigta:modal.trigger name="confirm-user-deletion">
            <sigta:button variant="danger" data-test="delete-user-button">
                {{ __('Delete account') }}
            </sigta:button>
        </sigta:modal.trigger>
    </div>

    <livewire:pages::settings.delete-user-modal />
</section>
