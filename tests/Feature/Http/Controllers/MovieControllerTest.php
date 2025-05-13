<?php

use App\Jobs\CreateMetrics;


it('can list movie 1 - A New Hope', function () {
    // enable middleware
    $this->withMiddleware(CreateMetrics::class);

    $response = $this->get('/movies/1');
    $response->assertStatus(200);
    $response->assertSee('A New Hope');

    $this->assertDatabaseHas('request_stats', [
        'url' => route('movies.show', 1),
        'http_request_method' => 'GET',
        'http_status_code' => 200,
    ]);
});
