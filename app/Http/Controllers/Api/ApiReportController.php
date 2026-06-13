<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiReportController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $today = today();

        return response()->json([
            'total_patients' => Patient::count(),
            'active_patients' => Patient::where('status', 'active')->count(),
            'pending_patients' => Patient::where('status', 'pending')->count(),
            'today_appointments' => Appointment::whereDate('start_at', $today)->count(),
            'today_completed' => Appointment::whereDate('start_at', $today)->where('status', 'completed')->count(),
            'upcoming_appointments' => Appointment::upcoming()->count(),
            'total_debt' => Transaction::getTotalDebt(),
            'arrived_today' => Appointment::whereDate('start_at', $today)->where('status', 'arrived')->count(),
        ]);
    }

    public function appointmentStats(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after:from',
        ]);

        $from = $request->from ? \Carbon\Carbon::parse($request->from) : now()->subMonth();
        $to = $request->to ? \Carbon\Carbon::parse($request->to) : now();

        $stats = Appointment::query()
            ->whereBetween('start_at', [$from, $to])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'by_status' => $stats,
            'total' => $stats->sum(),
        ]);
    }
}
