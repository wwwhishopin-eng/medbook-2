<?php

namespace App\Http\Controllers;

use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    public function show(LicenseService $licenseService)
    {
        $status = $licenseService->getExpiryStatus();

        return view('subscription.show', compact('status'));
    }

    public function renew(Request $request, LicenseService $licenseService): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|string',
            'period' => 'required|in:monthly,yearly',
        ]);

        return redirect()->route('subscription.show')
            ->with('status', 'subscription-renewal-requested');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $subscription = auth()->user()->subscriptions()->latest()->first();

        if ($subscription) {
            $subscription->cancel();
        }

        return redirect()->route('subscription.show')
            ->with('status', 'subscription-cancelled');
    }
}
