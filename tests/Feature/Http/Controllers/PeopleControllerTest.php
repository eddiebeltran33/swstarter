<?php

const PERSON_1 = [
    "created" => "2025-05-06T16:05:44.698Z",
    "edited" => "2025-05-06T16:05:44.698Z",
    "name" => "Luke Skywalker",
    "gender" => "male",
    "skin_color" => "fair",
    "hair_color" => "blond",
    "height" => "172",
    "eye_color" => "blue",
    "mass" => "77",
    "homeworld" => "https://www.swapi.tech/api/planets/1",
    "birth_year" => "19BBY",
    "url" => "https://www.swapi.tech/api/people/1"
];


test('It returns a person by id and its movies', function () {
    $response = $this->get('/api/v1/people/1');

    $response->assertStatus(200)
        ->assertJsonStructure(
            [
                "data" => [
                    'birth_year',
                    'gender',
                    'eye_color',
                    'hair_color',
                    'height',
                    'mass',
                    'name',
                    'movies' => [
                        '*' => [
                            'title',
                            'id',
                        ]
                    ]
                ]
            ]
        );
    $response->assertJsonFragment(
        [
            "name" => "Luke Skywalker",
        ]
    );

    $response->assertJsonFragment(
        [
            "title" => "A New Hope",
        ]
    );
});


test(
    'it returns the first page of people',
    function () {
        $response = $this->get('/api/v1/people');

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    "data" => [
                        '*' => [
                            'name',
                        ]
                    ]
                ]
            );
        $response->assertJsonFragment(
            [
                'name' => 'Luke Skywalker',
            ]
        );
    }
);

test('it can filter people by name', function () {
    $response = $this->get('/api/v1/people?search=Luke');

    $response->assertStatus(200)
        ->assertJsonStructure(
            [
                "data" => [
                    '*' => [
                        'name',
                    ]
                ]
            ]
        );
    $response->assertJsonFragment(
        [
            'name' => 'Luke Skywalker',
        ]
    );
    //asert that we dont see Darth Vader
    $response->assertJsonMissing(
        [
            'name' => 'Darth Vader',
        ]
    );
});
