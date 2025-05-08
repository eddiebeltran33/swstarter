<?php

namespace App\Jobs;

use App\Models\QueryStat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessQueryStat implements ShouldQueue
{
    use Queueable;

    protected array $data;
    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $event = $this->data;

        QueryStat::create([
            'action' => $event['action'],
            'outcome' => $event['outcome'],
            'started_at' => $event['started_at'],
            'ended_at' => $event['ended_at'],
            'duration' => $event['duration'],
            'http_request_method' => $event['http_request_method'],
            'client_ip' => $event['client_ip'],
            'url' => $event['url'],
            'search_term' => $event['search_term'],
            'resource_id' => $event['resource_id'],
        ]);
    }
}
