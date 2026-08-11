<x-kit-shell title="OTP">
    <x-kit-example label="Six digits (paste, arrows, backspace)">
    «sigta:otp :length="6" name="code" label="Verification code" description="Paste the code from your email — it splits across the boxes." />
    </x-kit-example>

    <x-kit-example label="Four digits">
    «sigta:otp :length="4" name="pin" label="PIN" />
    </x-kit-example>

    <x-kit-example label="Error state">
    «sigta:otp :length="6" name="badcode" label="Verification code" error="That code has expired." />
    </x-kit-example>
</x-kit-shell>
