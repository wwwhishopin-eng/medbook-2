<?php

namespace App\Console\Commands;

use App\Models\WaitingList;
use App\Models\Appointment;
use App\Services\SMS\SmsService;
use App\Services\SlotSuggestionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckWaitingList extends Command
{
    protected $signature = 'waitinglist:check';
    protected $description = 'Check for cancelled appointments and notify waiting list patients';

    public function handle(SmsService $smsService): int
    {
        $this->info('Checking waiting list for notifications...');

        // Check for recently cancelled appointments
        $cancelledAppointments = Appointment::query()
            ->where('status', Appointment::STATUS_CANCELLED)
            ->where('updated_at', '>=', now()->subMinutes(30))
            ->with('patient')
            ->get();

        foreach ($cancelledAppointments as $appointment) {
            $this->notifyWaitingListForSlot($appointment, $smsService);
        }

        $this->info('Waiting list check complete.');

        return Command::SUCCESS;
    }

    private function notifyWaitingListForSlot(Appointment $appointment, SmsService $smsService): void
    {
        // Find waiting list entries that match the time slot
        $waitingPatients = WaitingList::query()
            ->where('status', WaitingList::STATUS_WAITING)
            ->when($appointment->start_at->toDateString(), function ($q, $date) {
                $q->whereNull('preferred_date')
                    ->orWhereDate('preferred_date', $date);
            })
            ->with('patient')
            ->orderBy('created_at')
            ->limit(3)
            ->get();

        foreach ($waitingPatients as $waiting) {
            if ($waiting->patient->phone) {
                $message = "نوبت خالی در مطب ایشان آمده است.\n";
                $message .= "لطفاً هرچه سریع‌تر تماس بگیرید.";

                $result = $smsService->send(
                    $waiting->patient->phone,
                    $message
                );

                if ($result['success']) {
                    $waiting->markAsNotified();
                    $this->info("Notified waiting list patient: {$waiting->patient->full_name}");
                }
            }
        }
    }
}
