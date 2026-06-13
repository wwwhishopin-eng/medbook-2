<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiPatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $patients = Patient::query()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy($request->sort ?? 'created_at', $request->dir ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($patients);
    }

    public function show(Patient $patient): JsonResponse
    {
        $patient->load(['medicalHistory' => fn($q) => $q->latest()->limit(10), 'appointments' => fn($q) => $q->latest()->limit(10)]);

        return response()->json($patient);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
            'national_id' => 'nullable|string|size:10|unique:patients,national_id',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'conditions' => 'nullable|array',
            'allergies' => 'nullable|array',
            'emergency_contact_name' => 'nullable|string|max:120',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,pending,recovered,inactive',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['avatar_color'] = Patient::randomAvatarColor();
        $patient = Patient::create($data);

        AuditLog::log(AuditLog::ACTION_CREATE, $patient);

        return response()->json($patient, 201);
    }

    public function update(Request $request, Patient $patient): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'sometimes|string|max:60',
            'last_name' => 'sometimes|string|max:60',
            'national_id' => 'nullable|string|size:10|unique:patients,national_id,' . $patient->id,
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'sometimes|in:male,female',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,O+,O-',
            'conditions' => 'nullable|array',
            'allergies' => 'nullable|array',
            'status' => 'sometimes|in:active,pending,recovered,inactive',
            'notes' => 'nullable|string|max:2000',
        ]);

        AuditLog::log(AuditLog::ACTION_UPDATE, $patient, $patient->getOriginal(), $data);
        $patient->update($data);

        return response()->json($patient);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        AuditLog::log(AuditLog::ACTION_DELETE, $patient);
        $patient->delete();

        return response()->json(null, 204);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $patients = Patient::search($request->q)
            ->select('id', 'first_name', 'last_name', 'phone', 'avatar_color', 'status')
            ->limit(10)
            ->get();

        return response()->json($patients);
    }
}
