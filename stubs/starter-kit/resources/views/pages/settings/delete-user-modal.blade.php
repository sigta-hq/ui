<sigta:modal name="confirm-user-deletion" :heading="__('Are you sure you want to delete your account?')">
    <form method="POST" wire:submit="deleteUser" class="flex flex-col gap-6">
        <sigta:text variant="muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
        </sigta:text>

        <sigta:input wire:model="password" :label="__('Password')" :error="$errors->first('password')" type="password" autocomplete="current-password" />

        <div class="flex justify-end gap-2">
            <sigta:modal.close name="confirm-user-deletion">
                <sigta:button variant="ghost">{{ __('Cancel') }}</sigta:button>
            </sigta:modal.close>

            <sigta:button variant="danger" type="submit" data-test="confirm-delete-user-button">
                {{ __('Delete account') }}
            </sigta:button>
        </div>
    </form>
</sigta:modal>
