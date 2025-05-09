<?php

namespace App\Http\Controllers;

use App\Services\SWAAPIClient;

class MovieController extends Controller
{
    /**
     * The SWAPI client instance.
     */
    protected SWAAPIClient $swapiClient;

    public function __construct(SWAAPIClient $swapiClient)
    {
        $this->swapiClient = $swapiClient;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the movie details from the SWAPI client
        $response = $this->swapiClient->getMovieById($id);

        if (! $response) {
            // return 404 and redirect to the index page (/)
            return redirect()->route('dashboard')
                ->withErrors(['message' => 'Movie not found']);
        }

        return inertia('Movies/Show', [
            'movie' => [
                'title' => $response->title,
                'opening_crawl' => $response->openingCrawl,
            ],
            'characters' => $response->characters,
        ]);
    }
}
