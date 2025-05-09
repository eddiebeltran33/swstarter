<?php

use App\Jobs\CreateMetrics;
use Illuminate\Support\Facades\Schedule;

$minutes = config('app.create_metrics_interval_in_minutes', 5);

Schedule::job(new CreateMetrics)
    ->cron("*/{$minutes} * * * *")
    ->withoutOverlapping(); // to avoid deadlocks
