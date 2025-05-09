<?php

use App\Jobs\CreateMetrics;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Schedule;


// Schedule::job(new CreateMetrics())->everyFiveMinutes();


Schedule::call(
    function () {
        CreateMetrics::dispatch()->delay(now()->addMinute());
    }
)->everyFiveMinutes();
