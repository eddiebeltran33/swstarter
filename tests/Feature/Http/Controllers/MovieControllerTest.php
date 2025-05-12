<?php

use App\Jobs\CreateMetrics;
use App\Models\Metric;

it('can list movie 1 - A New Hope', function () {
    // enable middleware
    $this->withMiddleware(CreateMetrics::class);

    $response = $this->get('/movies/1');
    $response->assertStatus(200);
    $response->assertSee('A New Hope');

    // dump(Metric::all());
});
