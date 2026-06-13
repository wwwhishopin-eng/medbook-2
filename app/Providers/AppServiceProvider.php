<?php

namespace App\Providers;

use App\Helpers\Persian;
use App\Models\Appointment;
use App\Models\MedicalHistory;
use App\Models\Patient;
use App\Models\Transaction;
use App\Observers\AuditModelObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Blade::directive('fa', fn ($expr) => "<?php echo \\App\\Helpers\\Persian::digits($expr); ?>");
        Blade::directive('faCurrency', fn ($expr) => "<?php echo \\App\\Helpers\\Persian::currency($expr); ?>");
        Blade::directive('faDate', fn ($expr) => "<?php echo \\App\\Helpers\\Persian::date($expr); ?>");
        Blade::directive('jalali', fn ($expr) => "<?php echo \\App\\Helpers\\JalaliDate::format($expr); ?>");
        Blade::directive('jalaliFull', fn ($expr) => "<?php echo \\App\\Helpers\\JalaliDate::format($expr, 'Y/m/d - H:i'); ?>");
        Blade::directive('jalaliDate', fn ($expr) => "<?php echo \\App\\Helpers\\JalaliDate::format($expr, 'Y/m/d'); ?>");

        if (Schema::hasTable('audit_logs')) {
            Patient::observe(AuditModelObserver::class);
            Appointment::observe(AuditModelObserver::class);
            MedicalHistory::observe(AuditModelObserver::class);
            Transaction::observe(AuditModelObserver::class);
        }
    }
}
