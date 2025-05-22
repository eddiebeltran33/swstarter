<?php

namespace Tests\Feature\Api;

use App\Models\Metric;
// Removed: use Illuminate\Foundation\Testing\RefreshDatabase;
// Removed: use Tests\TestCase;
// Note: TestCase and RefreshDatabase are typically handled by tests/Pest.php

test('gets metrics list successfully', function () {
    // Create some metric data using the factory
    $metrics = Metric::factory()->count(3)->create();
    $firstMetric = $metrics->first();

    $response = $this->getJson('/api/v1/metrics');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [ // '*' means each item in the array
                    'id',
                    'action',
                    'outcome',
                    'started_at',
                    'ended_at',
                    'duration',
                    'http_request_method',
                    'client_ip',
                    'url',
                    'search_term',
                    'resource_id',
                    'http_status_code',
                    'created_at',
                    'updated_at',
                ],
            ],
            // No pagination meta expected for this endpoint as per current controller structure
        ])
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment([ // Check if one of the created metric's data is present
            'action' => $firstMetric->action,
            'url' => $firstMetric->url,
        ]);
});
