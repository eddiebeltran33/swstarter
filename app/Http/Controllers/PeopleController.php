<?php

namespace App\Http\Controllers;

use App\Services\SWAAPIClient;


class PeopleController extends Controller
{
    protected SWAAPIClient $swapiClient;

    public function __construct(SWAAPIClient $swapiClient)
    {
        $this->swapiClient = $swapiClient;
    }


    function show(int $id)
    {
        $response = $this->swapiClient->getPersonById($id);
        if (!$response) {
            return redirect(status: 404)->route('dashboard')->with('error', 'Person not found');
        }

        return inertia('People/Show', [
            'person' => [
                'name' => $response->name,
                'birth_year' => $response->birthYear,
                'gender' => $response->gender,
                'eye_color' => $response->eyeColor,
                'hair_color' => $response->hairColor,
                'height' => $response->height,
                'mass' => $response->mass,
            ],
            'movies' => $response->movies
        ]);
    }
}
