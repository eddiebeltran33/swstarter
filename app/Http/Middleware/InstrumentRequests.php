<?php

namespace App\Http\Middleware;

use App\Jobs\ProcessQueryStat;
use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;

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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response): void
    {
        $route = $request->route();

        $parameters = $route->parameters();

        $resourceIdValue = Arr::first($parameters, default: null);

        $originalStartTime = $request->server('REQUEST_TIME_FLOAT', microtime(true));
        $originalEndTime = microtime(true);

        // Convert timestamps to microsecond precision datetime strings
        $startDatetime = Carbon::createFromTimestampMs($originalStartTime * 1000);
        $endDatetime = Carbon::createFromTimestampMs($originalEndTime * 1000);

        $event = [
            'action' => $this->getValueOrNull($route->getActionName()),
            'outcome' => $response->isSuccessful() ? 'success' : 'failure',
            'started_at' => $startDatetime->format('Y-m-d H:i:s.u'),
            'ended_at' => $endDatetime->format('Y-m-d H:i:s.u'),
            'duration' => ($originalEndTime - $originalStartTime) * 1000, // in milliseconds
            'http_request_method' => $this->getValueOrNull($request->method()),
            'client_ip' => $this->getValueOrNull($request->ip()),
            'url' => $this->getValueOrNull($request->getUri()),
            'search_term' => $this->getValueOrNull($request->input('search')), // This remains, might be null for show routes
            'resource_id' => $this->getValueOrNull($resourceIdValue), // Use the dynamically determined resource ID
        ];

        ProcessQueryStat::dispatch($event);
    }

    /**
     * Get value or return null.
     *
     * @param mixed $value
     * @return mixed
     */
    public function getValueOrNull($value)
    {
        // Ensure empty strings, 0, false are not converted to null unless they actually are null
        return $value === '' || $value === 0 || $value === false ? $value : ($value ?: null);
    }
}
