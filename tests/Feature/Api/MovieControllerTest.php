<?php

namespace Tests\Feature\Api;

use App\Services\SWAAPI\Data\MovieDetailDTO;
use App\Services\SWAAPI\Data\MovieSummaryDTO;
use App\Services\SWAAPI\Data\PersonSummaryDTO;
// Removed: use Illuminate\Foundation\Testing\RefreshDatabase;
// Removed: use Tests\TestCase;
// Note: TestCase and RefreshDatabase are typically handled by tests/Pest.php

test('gets movies list successfully', function () {
    $mockClient = $this->mockSWAAPIClient();

    $dummyMovies = [
        new MovieSummaryDTO(id: 1, title: 'A New Hope', uid: '1', url: 'url/1', characterUrls: ['url/people/1']),
        new MovieSummaryDTO(id: 2, title: 'The Empire Strikes Back', uid: '2', url: 'url/2', characterUrls: ['url/people/2']),
    ];

    $mockClient->shouldReceive('getMovies')
        ->once()
        ->andReturn($dummyMovies);

    $response = $this->getJson('/api/v1/movies');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'uid', 'url', 'character_urls']
            ]
        ])
        ->assertJson([
            'data' => [
                ['id' => 1, 'title' => 'A New Hope', 'uid' => '1', 'url' => 'url/1', 'character_urls' => ['url/people/1']],
                ['id' => 2, 'title' => 'The Empire Strikes Back', 'uid' => '2', 'url' => 'url/2', 'character_urls' => ['url/people/2']],
            ]
        ]);
});

test('gets movie by id successfully', function () {
    $mockClient = $this->mockSWAAPIClient();
    $createdTime = now();
    $editedTime = now();

    $movieDetail = new MovieDetailDTO(
        id: 1,
        title: 'A New Hope',
        director: 'George Lucas',
        releaseDate: '1977-05-25',
        openingCrawl: 'It is a period of civil war...',
        characters: [
            new PersonSummaryDTO(id: 1, name: 'Luke Skywalker', uid: '1', url: 'url/people/1'),
        ],
        properties: [
            'title' => 'A New Hope',
            'director' => 'George Lucas',
            'release_date' => '1977-05-25',
            'opening_crawl' => 'It is a period of civil war...',
            // uid is part of the main DTO, not properties
        ],
        description: 'A movie from a galaxy far, far away.', // More specific description
        created: $createdTime->toIso8601String(),
        edited: $editedTime->toIso8601String(),
        uid: '1'
    );

    $mockClient->shouldReceive('getMovieById')
        ->once()
        ->with(1)
        ->andReturn($movieDetail);

    $response = $this->getJson('/api/v1/movies/1');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id', 'title', 'director', 'release_date', 'opening_crawl',
                'characters' => [
                    '*' => ['id', 'name', 'uid', 'url']
                ],
                'properties', 'description', 'created', 'edited', 'uid'
            ]
        ])
        ->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'A New Hope',
                'director' => 'George Lucas',
                'release_date' => '1977-05-25',
                'opening_crawl' => 'It is a period of civil war...',
                'characters' => [
                    ['id' => 1, 'name' => 'Luke Skywalker', 'uid' => '1', 'url' => 'url/people/1'],
                ],
                'properties' => [
                    'title' => 'A New Hope',
                    'director' => 'George Lucas',
                    'release_date' => '1977-05-25',
                    'opening_crawl' => 'It is a period of civil war...',
                ],
                'description' => 'A movie from a galaxy far, far away.',
                'created' => $createdTime->toIso8601String(),
                'edited' => $editedTime->toIso8601String(),
                'uid' => '1'
            ]
        ]);
});

test('returns 404 when movie not found', function () {
    $mockClient = $this->mockSWAAPIClient();

    $mockClient->shouldReceive('getMovieById')
        ->once()
        ->with(999) // A non-existent ID
        ->andReturn(null);

    $response = $this->getJson('/api/v1/movies/999');

    $response->assertStatus(404);
});
