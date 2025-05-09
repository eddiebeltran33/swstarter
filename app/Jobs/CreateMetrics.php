<?php

namespace App\Jobs;

use App\Models\Metric;
use App\Models\RequestStat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Log;

class CreateMetrics implements ShouldQueue
{
    use Queueable;
    const RESOURCE_TYPE_PEOPLE = 'people';
    const RESOURCE_TYPE_MOVIE = 'movie';

    public Carbon $startAt;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->startAt = now()->second(0)->millisecond(0);
        $this->onQueue('metric-aggregation'); // Specify the queue name if needed
        $this->delay(now()->addMinute()); // Delay the job by 1 minute
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //if this job was set to run at 2025-01-01 00:00:00, it should be handled at 2025-01-01 00:01:00 because of the delay
        //So this gives us a minute to allow request_stats to be done processing

        $startTimeForMetrics = $this->startAt->copy()->subMinutes(5);
        $endTimeForMetrics = $this->startAt;
        // Log::info("job is running start {$startTimeForMetrics->toDateTimeString()} end {$endTimeForMetrics->toDateTimeString()}");

        $this->calculateTopResourcesVisited($startTimeForMetrics, $endTimeForMetrics, self::RESOURCE_TYPE_PEOPLE);
        $this->calculateTopSearchTerms($startTimeForMetrics, $endTimeForMetrics, self::RESOURCE_TYPE_PEOPLE);
        $this->calculateTopResourcesVisited($startTimeForMetrics, $endTimeForMetrics, self::RESOURCE_TYPE_MOVIE);
        $this->calculateTopSearchTerms($startTimeForMetrics, $endTimeForMetrics, self::RESOURCE_TYPE_MOVIE);
        $this->calculateAverageRequestDuration($startTimeForMetrics, $endTimeForMetrics);
        $this->calculateTotalErrors($startTimeForMetrics, $endTimeForMetrics);
    }

    private function storeMetric(string $name, Carbon $start, Carbon $end, $valueData): void
    {
        // Ensure value is JSON encoded if it's an array/object
        $valueJson = is_string($valueData) ? $valueData : json_encode($valueData);

        Metric::insert([
            'name' => $name,
            'value' => $valueJson,
            'start_at' => $start,
            'end_at' => $end,
        ]);
    }

    private function calculateTopResourcesVisited(Carbon $start, Carbon $end, string $resourceType): void
    {

        $queryResult = RequestStat::query()
            ->select('resource_id', DB::raw('COUNT(*) as count'))
            ->where('action', 'LIKE', "%{$resourceType}%")
            ->whereNotNull('resource_id')
            ->where('resource_id', '!=', '') // Exclude empty strings if they can occur
            ->where('started_at', '>=', $start)
            ->where('ended_at', '<', $end)
            ->where('outcome', 'success') // Only count successful requests
            ->groupBy('resource_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        if ($queryResult->isNotEmpty()) {
            $this->storeMetric(
                "top_{$resourceType}_resources_visited",
                $start,
                $end,
                $queryResult
            );
        }
    }

    private function calculateTopSearchTerms(Carbon $start, Carbon $end, string $resourceType): void
    {


        $queryResult = RequestStat::query()
            ->select('search_term', DB::raw('COUNT(*) as count'))
            ->where('action', 'LIKE', "%$resourceType%")
            ->whereNotNull('search_term')
            ->where('search_term', '!=', '')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            // ->where('outcome', 'success')
            ->groupBy('search_term')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        if ($queryResult->isNotEmpty()) {
            $this->storeMetric(
                "top_{$resourceType}_search_terms",
                $start,
                $end,
                $queryResult
            );
        }
    }

    private function calculateAverageRequestDuration(Carbon $start, Carbon $end): void
    {
        $averageDuration = RequestStat::query()
            ->where('started_at', '>=', $start)
            ->where('started_at', '<', $end)
            ->avg('duration');

        if ($averageDuration === null) {
            return;
        }

        $this->storeMetric(
            'average_request_duration',
            $start,
            $end,
            ['average_duration_ms' => $averageDuration !== null ? (float) $averageDuration : null]
        );
    }

    private function calculateTotalErrors(Carbon $start, Carbon $end): void
    {
        $totalErrors = RequestStat::query()
            ->where('outcome', 'failure')
            ->where('started_at', '>=', $start)
            ->where('started_at', '<', $end)
            ->count();
        if ($totalErrors === 0) {
            return;
        }

        $this->storeMetric(
            'total_errors',
            $start,
            $end,
            ['total_errors' => $totalErrors]
        );
    }
}
