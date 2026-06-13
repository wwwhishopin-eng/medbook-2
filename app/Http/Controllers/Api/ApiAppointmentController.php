<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Services\SlotSuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiAppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $appointments = Appointment::query()
            ->with('patient')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date, fn($q) => $q->whereDate('start_at', $request->date))
            ->when($request->patient_id, fn($q) => $q->where('patient_id', $request->patient_id))
            ->orderBy('start_at')
            ->paginate($request->per_page ?? 15);

        return response()->json($appointments);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $appointment->load('patient', 'doctor');

        return response()->json($appointment);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'title' => 'required|string|max:255',
            'start_at' => 'required|date|after:now',
            'end_at' => 'nullable|date|after:start_at',
            'type' => 'nullable|in:checkup,follow_up,lab,consultation,emergency',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['created_by'] = $request->user()->id;
        $data['status'] = Appointment::STATUS_RESERVED;
        if (empty($data['end_at'])) {
            $data['end_at'] = \Carbon\Carbon::parse($data['start_at'])->addMinutes(30);
        }

        $appointment = Appointment::create($data);

        AuditLog::log(AuditLog::ACTION_CREATE, $appointment);

        return response()->json($appointment->load('patient'), 201);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'start_at' => 'sometimes|date',
            'end_at' => 'nullable|date|after:start_at',
            'status' => 'sometimes|in:reserved,confirmed,arrived,completed,cancelled,no_show',
            'type' => 'nullable|in:checkup,follow_up,lab,consultation,emergency',
            'notes' => 'nullable|string|max:2000',
        ]);

        AuditLog::log(AuditLog::ACTION_UPDATE, $appointment, $appointment->getOriginal(), $data);
        $appointment->update($data);

        return response()->json($appointment->load('patient'));
    }

    public function cancel(Appointment $appointment): JsonResponse
    {
        AuditLog::log(AuditLog::ACTION_UPDATE, $appointment);
        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return response()->json($appointment);
    }

    public function availableSlots(Request $request, SlotSuggestionService $slotService): JsonResponse
    {
        $request->validate(['date' => 'nullable|date']);

        $suggestions = $slotService->getSuggestions($request->date, 10);

        return response()->json($suggestions);
    }

    public function today(Request $request): JsonResponse
    {
        $appointments = Appointment::query()
            ->with('patient')
            ->whereDate('start_at', today())
            ->whereIn('status', [Appointment::STATUS_RESERVED, Appointment::STATUS_CONFIRMED, Appointment::STATUS_ARRIVED])
            ->orderBy('start_at')
            ->get();

        return response()->json($appointments);
    }
}
