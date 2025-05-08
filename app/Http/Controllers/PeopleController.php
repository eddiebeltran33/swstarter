<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeopleController extends Controller
{
    function show()
    {
        return inertia('People/Show', [
            'person' => [
                'name' => 'Luke Skywalker',
                'birth_year' => '24BBY',
                'gender' => 'male',
                'eye_color' => 'brown',
                'hair_color' => 'black',
                'height' => 183,
                'mass' => 85,
            ],
            'movies' => [
                [
                    'title' => 'A New Hope',
                    'id' => "1",
                ],
                [
                    'title' => 'The Empire Strikes Back',
                    'id' => "2",
                ],
                [
                    'title' => 'Return of the Jedi',
                    'id' => "3",
                ],
            ]
        ]);
    }
}
