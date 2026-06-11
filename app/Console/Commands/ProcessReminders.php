<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use App\Models\Subscription;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessReminders extends Command
{
    protected $signature = 'reminders:process';
    protected $description = 'Process appointment reminders (SMS and voice calls)';

    public function handle(ReminderService $reminderService, SmsService $smsService): int
    {
        $this->info('Processing appointment reminders...');

        $reminderService->processReminders();
        $this->info('Reminder processing complete for SMS and voice calls.');

        $this->info('Checking for subscription expiration alerts...');
        $subscriptions = Subscription::query()
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->with('user')
            ->get();

        foreach ($subscriptions as $subscription) {
            $daysRemaining = $subscription->daysRemaining();
            $mobile = $subscription->user?->phone;

            if ($mobile && in_array($daysRemaining, [7, 3, 1])) {
                $smsService->sendExpirationAlert($mobile, $daysRemaining);
                $this->info("Sent expiration alert to user ID: {$subscription->user_id} - {$daysRemaining} days remaining");
            }
        }

        $this->info('Expiration alert processing complete.');

        return Command::SUCCESS;
    }
}
