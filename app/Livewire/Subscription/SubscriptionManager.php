<?php

namespace App\Livewire\Subscription;

use App\Helpers\JalaliDate;
use App\Models\SubscriptionPlan;
use App\Services\LicenseService;
use App\Services\SmsService;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\View\View;

class SubscriptionManager extends Component
{
    public string $activeTab = 'status';

    public ?array $expStatus = null;
    public $plans = null;

    public bool $showRenewalModal = false;
    public string $selectedPlanId = '';
    public string $selectedPeriod = 'monthly';

    public function mount(): void
    {
        $this->loadStatus();
        $this->plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    public function loadStatus(): void
    {
        $service = app(LicenseService::class);
        $this->expStatus = $service->getExpiryStatus();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function selectPlan(string $planId): void
    {
        $this->selectedPlanId = $planId;
        $this->selectedPeriod = 'monthly';
        $this->showRenewalModal = true;
    }

    public function renewSubscription(): void
    {
        if (!auth()->check()) {
            return;
        }

        $plan = SubscriptionPlan::find($this->selectedPlanId);
        if (!$plan) {
            $this->addError('plan', 'پلن انتخاب شده معتبر نیست.');
            return;
        }

        $months = $this->selectedPeriod === 'yearly' ? 12 : 1;

        $this->dispatch('notify',
            message: "درخواست تمدید اشتراک ثبت شد. به زودی با شما تماس خواهیم گرفت.",
            type: 'success'
        );

        $this->showRenewalModal = false;
        $this->loadStatus();
    }

    public function dismissAlert(): void
    {
        session()->forget('subscription_warning');
    }

    public function getStatusBadgeStyle(): string
    {
        if (!$this->expStatus) return 'background:#F3F4F6;color:#6B7280';

        return match ($this->expStatus['status_label'] ?? '') {
            'فعال' => 'background:#DCFCE7;color:#15803D',
            'نسخه آزمایشی' => 'background:#EEF4FF;color:#1D4ED8',
            'در انتظار پرداخت' => 'background:#FEF9C3;color:#854D0E',
            'منقضی شده' => 'background:#FEE2E2;color:#991B1B',
            'لغو شده' => 'background:#F3F4F6;color:#6B7280',
            default => 'background:#F3F4F6;color:#6B7280',
        };
    }

    #[On('subscription-updated')]
    public function refresh(): void
    {
        $this->loadStatus();
    }

    public function render(): View
    {
        return view('livewire.subscription.subscription-manager');
    }
}
