<?php

namespace App\Livewire\Settings;

use App\Models\PushSubscription;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PushNotificationSettings extends Component
{
    public bool $pushSupported = false;
    public bool $subscribed = false;
    public string $statusMessage = '';

    public function mount(): void
    {
        $this->subscribed = PushSubscription::where('user_id', auth()->id())->exists();
    }

    public function render(): View
    {
        return view('livewire.settings.push-notification-settings');
    }
}
