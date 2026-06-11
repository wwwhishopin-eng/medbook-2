<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CheckLicense
{
    protected LicenseService $licenseService;

    protected array $allowedRoutes = [
        'subscription.renew',
        'subscription.show',
        'subscription.payment',
        'logout',
        'login',
        'register',
        'password.request',
        'password.email',
        'password.reset',
        'password.store',
        'verification.notice',
        'verification.verify',
        'verification.send',
    ];

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }

        $currentRoute = Route::currentRouteName();

        if (in_array($currentRoute, $this->allowedRoutes)) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->is('livewire/*')) {
            return $next($request);
        }

        if ($this->licenseService->shouldLockSystem() && !$this->isAllowedRoute($currentRoute)) {
            return redirect()->route('subscription.show')
                ->with('error', 'اشتراک شما منقضی شده است. لطفا برای تمدید اقدام کنید.');
        }

        if ($this->licenseService->shouldShowExpirationAlert()) {
            $status = $this->licenseService->getExpiryStatus();
            session()->flash('subscription_warning', $status);
        }

        return $next($request);
    }

    protected function isAllowedRoute(?string $routeName): bool
    {
        if (!$routeName) {
            return false;
        }

        foreach ($this->allowedRoutes as $allowed) {
            if (str_starts_with($routeName, $allowed)) {
                return true;
            }
        }

        return false;
    }
}
