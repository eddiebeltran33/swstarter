<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovieController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return inertia('Movies/Show', [
            'movie' => [
                'title' => 'A New Hope',
                'opening_crawl' => 'It is a period of civil war. Rebel spaceships, striking from a hidden base, have won their first victory against the evil Galactic Empire.',
            ],
            "characters" => [
                [
                    'name' => 'Luke Skywalker',
                    "id" => 1,
                ]
            ],
        ]);
    }
}
