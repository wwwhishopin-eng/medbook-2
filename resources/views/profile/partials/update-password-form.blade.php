<section>
    <header style="margin-bottom:20px;">
        <h4 style="font-size:15px;font-weight:700;color:#111A6B;margin:0 0 6px;">
            {{ __('Update Password') }}
        </h4>
        <p style="font-size:13px;color:#6B7280;margin:0;">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div style="margin-bottom:16px;">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div style="margin-bottom:16px;">
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div style="margin-bottom:16px;">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'password-updated')
                <p style="font-size:13px;color:#15803D;">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
