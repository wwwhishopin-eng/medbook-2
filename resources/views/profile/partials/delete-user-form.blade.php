<section>
    <header style="margin-bottom:16px;">
        <h4 style="font-size:15px;font-weight:700;color:#991B1B;margin:0 0 6px;">
            {{ __('Delete Account') }}
        </h4>
        <p style="font-size:13px;color:#6B7280;margin:0;">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" style="padding:24px;">
            @csrf
            @method('delete')

            <h4 style="font-size:16px;font-weight:700;color:#111A6B;margin:0 0 8px;">
                {{ __('Are you sure you want to delete your account?') }}
            </h4>

            <p style="font-size:13px;color:#6B7280;margin:0 0 20px;">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div style="margin-bottom:16px;">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
