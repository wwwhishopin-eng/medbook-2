<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size:22px;font-weight:800;color:#111A6B;margin:0;font-family:'Vazirmatn',sans-serif;">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="page-container" dir="rtl">
        <div class="page-inner" style="max-width:720px;">
            <div class="card" style="padding:24px;margin-bottom:20px;">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="card" style="padding:24px;margin-bottom:20px;">
                @include('profile.partials.update-password-form')
            </div>

            <div class="card" style="padding:24px;margin-bottom:20px;border:2px solid #FEE2E2;">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
