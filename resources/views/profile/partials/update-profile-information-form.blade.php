<section>
    <header style="margin-bottom:20px;">
        <h4 style="font-size:15px;font-weight:700;color:#111A6B;margin:0 0 6px;">
            {{ __('Profile Information') }}
        </h4>
        <p style="font-size:13px;color:#6B7280;margin:0;">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div style="margin-bottom:16px;">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div style="margin-bottom:16px;">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top:8px;">
                    <p style="font-size:13px;color:#374151;">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" style="color:#2E5BFF;text-decoration:underline;background:none;border:none;cursor:pointer;font-size:13px;font-family:Vazirmatn,sans-serif;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p style="font-size:13px;color:#15803D;font-weight:500;margin-top:4px;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p style="font-size:13px;color:#15803D;">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
