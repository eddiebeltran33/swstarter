<?php

test('It returns a film by id and its characters', function () {
    $response = $this->get('/api/v1/movies/1');

    $response->assertStatus(200)
        ->assertJsonStructure(
            [
                'data' => [
                    'title',
                    'characters' => [
                        '*' => [
                            'name',
                            'id',
                        ],
                    ],
                ],
            ]
        );
    $characters = collect($response->json('data.characters'));
    $this->assertTrue($characters->contains('name', 'C-3PO'));
    // $this->assertTrue($characters->contains('id', '2'));
});

test(
    'it returns all the movies if no filter is applied',
    function () {
        $response = $this->get('/api/v1/movies');

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'title',
                        ],
                    ],
                ]
            );
        $response->assertJsonFragment(
            [
                'title' => 'The Phantom Menace',
            ]
        );

        // assert that we get exactly 6 movies
        $response->assertJsonCount(6, 'data');
    }
);

test('it can filter movies by title', function () {
    $response = $this->get('/api/v1/movies?search=Jedi');

    $response->assertStatus(200)
        ->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'title',
                    ],
                ],
            ]
        );
    $response->assertJsonFragment(
        [
            'title' => 'Return of the Jedi',
        ]
    );
    // asert that we dont see any other movie
    $response->assertJsonMissing(
        [
            'title' => 'The Phantom Menace',
        ]
    );
});
