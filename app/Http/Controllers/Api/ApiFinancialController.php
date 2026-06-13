<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiFinancialController extends Controller
{
    public function transactions(Request $request): JsonResponse
    {
        $transactions = Transaction::query()
            ->with(['patient', 'creator'])
            ->when($request->patient_id, fn($q) => $q->where('patient_id', $request->patient_id))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderBy('transaction_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($transactions);
    }

    public function storeTransaction(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|in:charge,payment',
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $data['created_by'] = $request->user()->id;
        $transaction = Transaction::create($data);

        AuditLog::log(AuditLog::ACTION_CREATE, $transaction);

        return response()->json($transaction, 201);
    }

    public function debtorList(Request $request): JsonResponse
    {
        $patients = \App\Models\Patient::query()
            ->whereHas('transactions')
            ->get()
            ->filter(fn($p) => $p->debt > 0)
            ->sortByDesc('debt')
            ->values()
            ->map(fn($p) => [
                'id' => $p->id,
                'full_name' => $p->full_name,
                'phone' => $p->phone,
                'debt' => $p->debt,
            ]);

        return response()->json([
            'total_debt' => Transaction::getTotalDebt(),
            'debtors' => $patients,
        ]);
    }
}
