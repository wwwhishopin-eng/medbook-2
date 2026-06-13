<?php

namespace App\Http\Controllers;

use App\Models\OnlineBookingSlot;
use App\Models\OnlineBooking;
use App\Models\Appointment;
use App\Services\SlotSuggestionService;
use App\Services\SMS\SmsService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OnlineBookingController extends Controller
{
    public function show(string $slug): View
    {
        $bookingSlot = OnlineBookingSlot::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $doctor = $bookingSlot->user;

        return view('booking.public', compact('bookingSlot', 'doctor'));
    }

    public function getAvailableSlots(string $slug, Request $request, SlotSuggestionService $slotService)
    {
        $bookingSlot = OnlineBookingSlot::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $request->validate(['date' => 'required|date|after:today']);

        $slots = $slotService->getAvailableSlotsForDay(\Carbon\Carbon::parse($request->date));

        $bookedTimes = OnlineBooking::where('online_booking_slot_id', $bookingSlot->id)
            ->whereDate('start_at', $request->date)
            ->whereIn('status', [OnlineBooking::STATUS_PENDING, OnlineBooking::STATUS_CONFIRMED])
            ->pluck('start_at')
            ->map(fn($d) => $d->format('Y-m-d H:i:s'))
            ->toArray();

        $availableSlots = collect($slots)->filter(fn($s) => !in_array($s['datetime'], $bookedTimes))->values();

        return response()->json($availableSlots);
    }

    public function book(string $slug, Request $request, SmsService $smsService): RedirectResponse
    {
        $bookingSlot = OnlineBookingSlot::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'patient_name' => 'required|string|max:120',
            'patient_phone' => 'required|string|max:20|regex:/^0[0-9]{10}$/',
            'patient_national_id' => 'nullable|string|size:10',
            'start_at' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
        ], [
            'patient_name.required' => 'نام و نام خانوادگی الزامی است.',
            'patient_phone.required' => 'شماره موبایل الزامی است.',
            'patient_phone.regex' => 'شماره موبایل نامعتبر است.',
            'start_at.required' => 'لطفاً ساعت نوبت را انتخاب کنید.',
        ]);

        $startAt = \Carbon\Carbon::parse($data['start_at']);
        $endAt = $startAt->copy()->addMinutes($bookingSlot->slot_duration);

        // Check if slot is already booked
        $exists = OnlineBooking::where('online_booking_slot_id', $bookingSlot->id)
            ->where('start_at', $startAt)
            ->whereIn('status', [OnlineBooking::STATUS_PENDING, OnlineBooking::STATUS_CONFIRMED])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'این نوبت قبلاً رزرو شده است. لطفاً ساعت دیگری انتخاب کنید.');
        }

        $booking = OnlineBooking::create([
            'online_booking_slot_id' => $bookingSlot->id,
            'patient_name' => $data['patient_name'],
            'patient_phone' => $data['patient_phone'],
            'patient_national_id' => $data['patient_national_id'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => OnlineBooking::STATUS_PENDING,
            'notes' => $data['notes'] ?? null,
        ]);

        // Send SMS confirmation
        $date = \App\Helpers\JalaliDate::format($startAt, 'Y/m/d');
        $time = $startAt->format('H:i');
        $smsService->send(
            $data['patient_phone'],
            "بیمار گرامی {$data['patient_name']}\nنوبت شما در تاریخ {$date} ساعت {$time} ثبت شد.\nدر انتظار تایید مطب می‌باشد."
        );

        return redirect()->route('booking.success', $slug)->with('success', 'نوبت شما با موفقیت ثبت شد!');
    }

    public function success(string $slug): View
    {
        $bookingSlot = OnlineBookingSlot::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('booking.success', compact('bookingSlot'));
    }
}
