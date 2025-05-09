<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SWAAPI\Data\MovieSummaryDTO;
use App\Services\SWAAPIClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    private SWAAPIClient $swapiClient;

    public function __construct(SWAAPIClient $swapiClient)
    {
        $this->swapiClient = $swapiClient;
    }

    public function index(Request $request): JsonResponse
    {
        $searchQuery = $request->query('search');

        $movieSummaries = $this->swapiClient->getMovies($searchQuery);

        $filmListData = array_map(function (MovieSummaryDTO $movieSummaryDto) {
            return [
                'title' => $movieSummaryDto->title,
                'id' => $movieSummaryDto->id,
            ];
        }, $movieSummaries);

        return response()->json(
            [
                'data' => $filmListData,
                // SWAPI /films endpoint doesn't seem to provide total_records directly in the main response,
                // so count of returned items is used.
                'total' => count($filmListData),
            ]
        );
    }

    public function show(int $id): JsonResponse
    {
        // $route = request()->route();
        // dd($route->parameters());
        $movieDto = $this->swapiClient->getMovieById($id);

        if (! $movieDto) {
            return response()->json(['message' => 'Film not found'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $movieDto->id,
                'title' => $movieDto->title,
                'opening_crawl' => $movieDto->openingCrawl,
                'director' => $movieDto->director,
                'producer' => $movieDto->producer,
                'release_date' => $movieDto->releaseDate,
                'characters' => $movieDto->characters,
            ],
        ]);
    }
}
