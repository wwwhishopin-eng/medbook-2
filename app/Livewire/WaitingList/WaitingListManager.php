<?php

namespace App\Livewire\WaitingList;

use App\Models\WaitingList;
use App\Models\Appointment;
use App\Services\SlotSuggestionService;
use App\Services\SMS\SmsService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class WaitingListManager extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $search = '';

    public bool $showAssignModal = false;
    public ?string $assignWaitingId = null;
    public string $selectedSlot = '';
    public array $availableSlots = [];

    public function boot(SlotSuggestionService $slotService): void
    {
        $this->slotService = $slotService;
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openAssignModal(string $waitingId): void
    {
        $this->assignWaitingId = $waitingId;
        $this->showAssignModal = true;

        // Load available slots for today and tomorrow
        $todaySlots = $this->slotService->getAvailableSlotsForDay(now());
        $tomorrowSlots = $this->slotService->getAvailableSlotsForDay(now()->addDay());
        $this->availableSlots = array_merge($todaySlots, $tomorrowSlots);
    }

    public function assignAppointment(SmsService $smsService): void
    {
        $waiting = WaitingList::findOrFail($this->assignWaitingId);

        $this->validate([
            'selectedSlot' => 'required',
        ], [
            'selectedSlot.required' => 'لطفاً ساعت نوبت را انتخاب کنید.',
        ]);

        $startAt = \Carbon\Carbon::parse($this->selectedSlot);
        $endAt = $startAt->copy()->addMinutes(30);

        $appointment = Appointment::create([
            'patient_id' => $waiting->patient_id,
            'created_by' => auth()->id(),
            'title' => 'ویزیت از لیست انتظار',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => Appointment::STATUS_CONFIRMED,
            'type' => 'checkup',
        ]);

        // Send SMS notification
        if ($waiting->patient->phone) {
            $date = \App\Helpers\JalaliDate::format($startAt, 'Y/m/d');
            $time = $startAt->format('H:i');
            $smsService->sendAppointmentReminder(
                $waiting->patient->phone,
                $waiting->patient->full_name,
                $date,
                $time
            );
        }

        $waiting->assign();

        $this->showAssignModal = false;
        $this->assignWaitingId = null;
        $this->selectedSlot = '';

        $this->dispatch('notify',
            message: "نوبت برای {$waiting->patient->full_name} ثبت شد.",
            type: 'success'
        );
    }

    public function cancelWaiting(string $waitingId): void
    {
        $waiting = WaitingList::findOrFail($waitingId);
        $waiting->cancel();

        $this->dispatch('notify',
            message: "درخواست {$waiting->patient->full_name} لغو شد.",
            type: 'success'
        );
    }

    public function notifyWaiting(string $waitingId, SmsService $smsService): void
    {
        $waiting = WaitingList::findOrFail($waitingId);

        if ($waiting->patient->phone) {
            $smsService->send(
                $waiting->patient->phone,
                "لطفاً برای تعیین نوبت با مطب تماس بگیرید."
            );
        }

        $waiting->markAsNotified();

        $this->dispatch('notify',
            message: "پیامک به {$waiting->patient->full_name} ارسال شد.",
            type: 'success'
        );
    }

    public function render(): View
    {
        $waitingList = WaitingList::query()
            ->with('patient')
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, function ($q) {
                $term = $this->search;
                $q->whereHas('patient', function ($pq) use ($term) {
                    $pq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.waiting-list.waiting-list-manager', [
            'waitingList' => $waitingList,
            'statuses' => WaitingList::STATUSES,
        ]);
    }
}
