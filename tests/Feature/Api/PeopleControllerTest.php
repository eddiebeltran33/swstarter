<?php

namespace Tests\Feature\Api;

use App\Services\SWAAPI\Data\PaginatedPeopleResponseDTO;
use App\Services\SWAAPI\Data\PersonDetailDTO;
use App\Services\SWAAPI\Data\PersonSummaryDTO;
// Removed: use Illuminate\Foundation\Testing\RefreshDatabase;
// Removed: use Tests\TestCase;
// Note: TestCase and RefreshDatabase are typically handled by tests/Pest.php

test('gets people list successfully', function () {
    $mockClient = $this->mockSWAAPIClient();

    $dummyPeople = [
        new PersonSummaryDTO(id: 1, name: 'Luke Skywalker', uid: '1', url: 'url/1'),
        new PersonSummaryDTO(id: 2, name: 'Leia Organa', uid: '2', url: 'url/2'),
    ];

    $paginatedResponse = new PaginatedPeopleResponseDTO(
        people: $dummyPeople,
        currentPage: 1,
        nextPage: null,
        perPage: 10,
        totalRecords: 2,
        totalPages: 1
    );

    $mockClient->shouldReceive('getPeople')
        ->once()
        ->andReturn($paginatedResponse);

    $response = $this->getJson('/api/v1/people');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'uid', 'url']
            ],
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ])
        ->assertJson([
            'data' => [
                ['id' => 1, 'name' => 'Luke Skywalker', 'uid' => '1', 'url' => 'url/1'],
                ['id' => 2, 'name' => 'Leia Organa', 'uid' => '2', 'url' => 'url/2'],
            ],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 2,
            ]
        ]);
});

test('gets person by id successfully', function () {
    $mockClient = $this->mockSWAAPIClient();

    $personDetail = new PersonDetailDTO(
        id: 1,
        uid: '1',
        name: 'Luke Skywalker',
        height: '172',
        mass: '77',
        hairColor: 'blond',
        skinColor: 'fair',
        eyeColor: 'blue',
        birthYear: '19BBY',
        gender: 'male',
        properties: [
            'height' => '172',
            'mass' => '77',
            'hair_color' => 'blond',
            'skin_color' => 'fair',
            'eye_color' => 'blue',
            'birth_year' => '19BBY',
            'gender' => 'male',
        ],
        description: 'A person',
        created: now()->toIso8601String(),
        edited: now()->toIso8601String(),
        homeworldName: 'Tatooine',
        homeworldUrl: 'url/tatooine',
        movies: [
            ['id' => 1, 'title' => 'A New Hope'],
        ]
    );

    $mockClient->shouldReceive('getPersonById')
        ->once()
        ->with(1)
        ->andReturn($personDetail);

    $response = $this->getJson('/api/v1/people/1');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id', 'uid', 'name', 'height', 'mass', 'hair_color', 'skin_color',
                'eye_color', 'birth_year', 'gender', 'properties', 'description',
                'created', 'edited', 'homeworld_name', 'homeworld_url', 'movies'
            ]
        ])
        ->assertJson([
            'data' => [
                'id' => 1,
                'uid' => '1',
                'name' => 'Luke Skywalker',
                'height' => '172',
                'mass' => '77',
                'hair_color' => 'blond',
                'skin_color' => 'fair',
                'eye_color' => 'blue',
                'birth_year' => '19BBY',
                'gender' => 'male',
                'properties' => [
                    'height' => '172',
                    'mass' => '77',
                    'hair_color' => 'blond',
                    'skin_color' => 'fair',
                    'eye_color' => 'blue',
                    'birth_year' => '19BBY',
                    'gender' => 'male',
                ],
                'description' => 'A person',
                'created' => $personDetail->created, // Assert against the dynamic value
                'edited' => $personDetail->edited,   // Assert against the dynamic value
                'homeworld_name' => 'Tatooine',
                'homeworld_url' => 'url/tatooine',
                'movies' => [
                    ['id' => 1, 'title' => 'A New Hope'],
                ]
            ]
        ]);
});

test('returns 404 when person not found', function () {
    $mockClient = $this->mockSWAAPIClient();

    $mockClient->shouldReceive('getPersonById')
        ->once()
        ->with(999)
        ->andReturn(null);

    $response = $this->getJson('/api/v1/people/999');

    $response->assertStatus(404);
});
