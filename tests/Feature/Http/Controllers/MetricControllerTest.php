<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Metric;
use Inertia\Testing\AssertableInertia;
// Removed: use Illuminate\Foundation\Testing\RefreshDatabase;
// Removed: use Tests\TestCase;
// Note: TestCase and RefreshDatabase are typically handled by tests/Pest.php

test('shows metrics page successfully', function () {
    // Seed data
    $metrics = Metric::factory()->count(5)->create();
    $firstMetric = $metrics->first(); // Get one for specific assertion

    $response = $this->get(route('metrics.index'));

    $response->assertStatus(200);

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Metrics/Index')
        ->has('metrics') // The prop passed to the view (paginator)
        ->has('metrics.data', 5) // Check count in paginated data for the current page
        // Assert structure or specific data of the first item
        ->where('metrics.data.0.id', $firstMetric->id)
        ->where('metrics.data.0.action', $firstMetric->action)
        ->where('metrics.data.0.outcome', $firstMetric->outcome)
        ->where('metrics.data.0.http_request_method', $firstMetric->http_request_method)
        ->where('metrics.data.0.client_ip', $firstMetric->client_ip)
        ->where('metrics.data.0.url', $firstMetric->url)
        ->where('metrics.data.0.search_term', $firstMetric->search_term)
        ->where('metrics.data.0.resource_id', $firstMetric->resource_id)
        ->where('metrics.data.0.http_status_code', $firstMetric->http_status_code)
        // Asserting the presence of all expected keys for each item in metrics.data
        ->has('metrics.data', 5, fn (AssertableInertia $item) => $item
            ->has('id')
            ->has('action')
            ->has('outcome')
            ->has('started_at') // Timestamps are usually present
            ->has('ended_at')
            ->has('duration')
            ->has('http_request_method')
            ->has('client_ip')
            ->has('url')
            ->has('search_term')
            ->has('resource_id')
            ->has('http_status_code')
            ->has('created_at')
            ->has('updated_at')
        )
        ->where('metrics.total', 5) // Total number of metrics
        ->where('metrics.per_page', 15) // As per controller's pagination
        ->where('metrics.current_page', 1) // Should be the first page
    );
});
