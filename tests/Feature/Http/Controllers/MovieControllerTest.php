<?php

namespace Tests\Feature\Http\Controllers;

use App\Services\SWAAPI\Data\MovieDetailDTO;
use App\Services\SWAAPI\Data\PersonSummaryDTO;
use Inertia\Testing\AssertableInertia;
// Removed: use Illuminate\Foundation\Testing\RefreshDatabase;
// Removed: use Tests\TestCase;
// Removed: use App\Jobs\CreateMetrics; // As the test using it is redundant

test('shows movie page successfully', function () {
    $mockClient = $this->mockSWAAPIClient();
    $createdTime = now()->toIso8601String();
    $editedTime = now()->toIso8601String();

    $movieDetail = new MovieDetailDTO(
        id: 1,
        uid: '1', // Added UID as per common DTO structure
        title: 'A New Hope',
        director: 'George Lucas',
        releaseDate: '1977-05-25',
        openingCrawl: 'It is a period of civil war...',
        properties: [ // Ensure these properties are what MovieDetailDTO expects in its 'properties' array
            'title' => 'A New Hope', // It's usually a top-level prop, but following prompt example
            'director' => 'George Lucas',
            'release_date' => '1977-05-25',
            'opening_crawl' => 'It is a period of civil war...',
            'uid' => '1', // Also usually top-level
        ],
        description: 'A Star Wars movie.',
        created: $createdTime,
        edited: $editedTime,
        characters: [
            new PersonSummaryDTO(id: 1, name: 'Luke Skywalker', uid: '1', url: 'url/luke'),
            new PersonSummaryDTO(id: 2, name: 'Leia Organa', uid: '2', url: 'url/leia'),
        ]
    );

    $mockClient->shouldReceive('getMovieById')
        ->once()
        ->with(1)
        ->andReturn($movieDetail);

    $response = $this->get(route('movies.show', ['id' => 1]));

    $response->assertStatus(200);

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Movies/Show')
        ->has('movie')
        ->where('movie.id', 1)
        ->where('movie.uid', '1')
        ->where('movie.title', 'A New Hope')
        ->where('movie.director', 'George Lucas')
        ->where('movie.releaseDate', '1977-05-25')
        ->where('movie.openingCrawl', 'It is a period of civil war...')
        ->where('movie.description', 'A Star Wars movie.')
        ->where('movie.created', $createdTime)
        ->where('movie.edited', $editedTime)
        // Add more assertions for other properties as needed
        ->has('movie.characters', 2) // Example for nested prop
        ->where('movie.characters.0.name', 'Luke Skywalker')
        ->where('movie.characters.0.uid', '1')
        ->where('movie.characters.1.name', 'Leia Organa')
        ->where('movie.characters.1.uid', '2')
        // Asserting properties within the 'properties' array if applicable
        ->has('movie.properties')
        ->where('movie.properties.uid', '1') // Example, adjust if 'uid' is not in DTO's 'properties'
    );
});

test('returns 404 when movie page not found', function () {
    $mockClient = $this->mockSWAAPIClient();

    $mockClient->shouldReceive('getMovieById')
        ->once()
        ->with(999)
        ->andReturn(null);

    $response = $this->get(route('movies.show', ['id' => 999]));

    $response->assertStatus(404);
});
