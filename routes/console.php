<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule reminder processing every 5 minutes
Schedule::command('reminders:process')->everyFiveMinutes()->withoutOverlapping();

// Schedule waiting list notification check every 30 minutes
Schedule::command('waitinglist:check')->everyThirtyMinutes()->withoutOverlapping();
