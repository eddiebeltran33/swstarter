<?php

namespace App\Http\Middleware;

use App\Jobs\ProcessQueryStat;
use App\Models\QueryStat;
use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response): void
    {
        $originalStartTime = $request->server('REQUEST_TIME_FLOAT');
        $originalEndTime = microtime(true);

        // Convert timestamps to microsecond precision datetime strings
        $startDatetime = Carbon::createFromTimestampMs($originalStartTime * 1000);
        $endDatetime = Carbon::createFromTimestampMs($originalEndTime * 1000);

        $event = [
            'action' => $this->getValueOrNull($request->route() ? $request->route()->getActionName() : null),
            'outcome' => $response->isSuccessful() ? 'success' : 'failure',
            'started_at' => $startDatetime->format('Y-m-d H:i:s.u'),
            'ended_at' => $endDatetime->format('Y-m-d H:i:s.u'),
            'duration' => ($originalEndTime - $originalStartTime) * 1000,
            'http_request_method' => $this->getValueOrNull($request->method()),
            'client_ip' => $this->getValueOrNull($request->ip()),
            'url' => $this->getValueOrNull($request->getUri()),
            'search_term' => $this->getValueOrNull($request->input('search')),
            'resource_id' => $this->getValueOrNull($request->route('id')),
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
        return $value ?: null;
    }
}
