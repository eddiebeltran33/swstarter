<?php

namespace App\Jobs;

use App\Models\Metric;
use App\Models\RequestStat;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class CreateMetrics implements ShouldQueue
{
    use Queueable;

    public Carbon $start;

    public Carbon $end;

    public function __construct()
    {
        // start at midnight today
        $this->start = now()->startOfDay();
        // end at the exact minute this job is dispatched
        $this->end = now()->endOfMinute()->second(0)->millisecond(0);

        $this->onQueue('metric-aggregation');
    }

    public function handle(): void
    {

        $this->calculateTopResourcesVisited($this->start, $this->end, 'people');
        $this->calculateTopSearchTerms($this->start, $this->end, 'people');
        $this->calculateTopResourcesVisited($this->start, $this->end, 'movie');
        $this->calculateTopSearchTerms($this->start, $this->end, 'movie');
        $this->calculateAverageRequestDuration($this->start, $this->end);
        $this->calculateTotalErrors($this->start, $this->end);
    }

    private function storeMetric(string $name, Carbon $start, Carbon $end, $valueData): void
    {
        $json = is_string($valueData) ? $valueData : json_encode($valueData);

        // upsert on (name + start + end)
        Metric::updateOrCreate(
            [
                'name' => $name,
                'start_at' => $start,
            ],
            [
                'value' => $json,
                'end_at' => $end,
            ]
        );
    }

    private function calculateTopResourcesVisited(Carbon $start, Carbon $end, string $resourceType): void
    {
        $rows = RequestStat::select('resource_id', DB::raw('COUNT(*) AS count'))
            ->where('action', 'LIKE', "%{$resourceType}%")
            ->whereNotNull('resource_id')
            ->where('resource_id', '!=', '')
            ->whereBetween('started_at', [$start, $end])
            ->where('outcome', 'success')
            ->groupBy('resource_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        if ($rows->isNotEmpty()) {
            $this->storeMetric("top_{$resourceType}_resources_visited", $start, $end, $rows);
        }
    }

    private function calculateTopSearchTerms(Carbon $start, Carbon $end, string $resourceType): void
    {
        $rows = RequestStat::select('search_term', DB::raw('COUNT(*) AS count'))
            ->where('action', 'LIKE', "%{$resourceType}%")
            ->whereNotNull('search_term')
            ->where('search_term', '!=', '')
            ->whereBetween('started_at', [$start, $end])
            ->groupBy('search_term')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        if ($rows->isNotEmpty()) {
            $this->storeMetric("top_{$resourceType}_search_terms", $start, $end, $rows);
        }
    }

    private function calculateAverageRequestDuration(Carbon $start, Carbon $end): void
    {
        $avg = RequestStat::whereBetween('started_at', [$start, $end])->avg('duration');

        if (! is_null($avg)) {
            $this->storeMetric('average_request_duration', $start, $end, [
                'average_duration_ms' => (float) $avg,
            ]);
        }
    }

    private function calculateTotalErrors(Carbon $start, Carbon $end): void
    {
        $errors = RequestStat::where('outcome', 'failure')
            ->whereBetween('started_at', [$start, $end])
            ->count();

        if ($errors > 0) {
            $this->storeMetric('total_errors', $start, $end, [
                'total_errors' => $errors,
            ]);
        }
    }
}
