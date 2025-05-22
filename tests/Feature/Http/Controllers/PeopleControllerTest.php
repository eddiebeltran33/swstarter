<?php

namespace Tests\Feature\Http\Controllers;

use App\Services\SWAAPI\Data\PersonDetailDTO;
use App\Services\SWAAPIClient; // Ensure SWAAPIClient is imported for mocking
use Inertia\Testing\AssertableInertia;
// Removed: use Illuminate\Foundation\Testing\RefreshDatabase;
// Removed: use Tests\TestCase;
// Note: TestCase and RefreshDatabase are typically handled by tests/Pest.php

test('shows person page successfully', function () {
    // Mock SWAAPIClient
    $mockSwaapiClient = $this->mockSWAAPIClient();

    // Create a dummy PersonDetailDTO
    $personData = new PersonDetailDTO(
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
            'homeworld' => 'url/tatooine', // Assuming homeworld URL is part of properties
            'url' => 'https://www.swapi.tech/api/people/1' // Assuming 'url' is part of properties
        ],
        description: 'A character from Star Wars.',
        created: now()->toIso8601String(),
        edited: now()->toIso8601String(),
        homeworldName: 'Tatooine',
        homeworldUrl: 'url/tatooine',
        movies: [
            ['id' => 4, 'title' => 'A New Hope'], // Assuming movie IDs might differ from person ID
            ['id' => 5, 'title' => 'The Empire Strikes Back']
        ]
    );

    $mockSwaapiClient->shouldReceive('getPersonById')
        ->once()
        ->with(1)
        ->andReturn($personData);

    // Make a GET request to the web route for showing a person
    // Assuming the route is named 'people.show' as per web.php
    $response = $this->get(route('people.show', ['id' => 1]));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('People/Show')
        ->has('person')
        ->where('person.id', 1)
        ->where('person.name', 'Luke Skywalker')
        ->where('person.uid', '1') // Ensure uid is asserted
        ->where('person.height', '172')
        ->where('person.mass', '77')
        ->where('person.hairColor', 'blond') // Corrected from hair_color to hairColor to match DTO
        ->where('person.skinColor', 'fair') // Corrected from skin_color to skinColor
        ->where('person.eyeColor', 'blue')   // Corrected from eye_color to eyeColor
        ->where('person.birthYear', '19BBY') // Corrected from birth_year to birthYear
        ->where('person.gender', 'male')
        ->where('person.homeworldName', 'Tatooine') // Corrected from homeworld_name to homeworldName
        ->has('person.movies', 2) // Asserting count of movies
        ->where('person.movies.0.title', 'A New Hope')
        ->where('person.movies.1.title', 'The Empire Strikes Back')
    );
});

test('returns 404 when person page not found', function () {
    // Mock SWAAPIClient
    $mockSwaapiClient = $this->mockSWAAPIClient();

    $mockSwaapiClient->shouldReceive('getPersonById')
        ->once()
        ->with(999)
        ->andReturn(null);

    // Make a GET request to a non-existent person ID
    $response = $this->get(route('people.show', ['id' => 999]));

    $response->assertStatus(404);
});
