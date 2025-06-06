<?php

namespace App\Http\Middleware;

use App\Jobs\ProcessRequestStat;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class InstrumentRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Perform actions after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $route = $request->route();

        $parameters = $route->parameters();

        $resourceIdValue = Arr::first($parameters, default: null);

        $originalStartTime = $request->server('REQUEST_TIME_FLOAT', microtime(true));
        $originalEndTime = microtime(true);

        // Convert timestamps to microsecond precision datetime strings
        $startDatetime = Carbon::createFromTimestampMs($originalStartTime * 1000, timezone: config('app.timezone'));
        $endDatetime = Carbon::createFromTimestampMs($originalEndTime * 1000, timezone: config('app.timezone'));

        $event = [
            'action' => $this->getValueOrNull($route->getActionName()),
            'outcome' => $response->isSuccessful() ? 'success' : 'failure',
            'started_at' => $startDatetime->format('Y-m-d H:i:s.u'),
            'ended_at' => $endDatetime->format('Y-m-d H:i:s.u'),
            'duration' => ($originalEndTime - $originalStartTime) * 1000, // in milliseconds
            'http_request_method' => $this->getValueOrNull($request->method()),
            'client_ip' => $this->getValueOrNull($request->ip()),
            'url' => $this->getValueOrNull($request->getUri()),
            'search_term' => $this->getValueOrNull($request->input('search')),
            'resource_id' => $this->getValueOrNull($resourceIdValue),
            'http_status_code' => $this->getValueOrNull($response->getStatusCode()),
        ];

        ProcessRequestStat::dispatch($event);
    }

    /**
     * Get value or return null.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getValueOrNull($value)
    {
        return $value === '' || $value === 0 || $value === false ? $value : ($value ?: null);
    }
}
