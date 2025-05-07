<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $response = Http::withoutVerifying() // just because im too lazy to set up a certificate
            ->get('https://swapi.tech/api/films', [
                'title' => $request->query('search'),
                'page' => $request->query('page'),
            ])
            ->json();

        return response()->json(
            [
                "data" => collect($response['result'])
                    ->map(function ($film) {
                        return [
                            'title' => $film['properties']["title"],
                            'id' => $film['uid'],
                        ];
                    }),
                "current_page" => $request->query('page', 1),
                "next_page" => $this->getPageFromUrl(optional($response)['next']),
                "per_page" => 10, // Assuming the API returns 10 results per page
                "total" => count($response['result']),
            ]
        );
    }

    private function getPageFromUrl(string | null $url): string | null
    {
        if (is_null($url)) {
            return null;
        }
        // Parse the URL to get query parameters
        $parsedUrl = parse_url($url);

        // If there are no query parameters, return null
        if (!isset($parsedUrl['query'])) {
            return null;
        }

        // Parse the query string into an array
        parse_str($parsedUrl['query'], $queryParams);

        // Check if page parameter exists and return it
        if (isset($queryParams['page'])) {
            return $queryParams['page'];
        }

        return null;
    }


    public function show(int $id)
    {

        $response = Http::withoutVerifying()
            ->get("https://swapi.tech/api/films/{$id}")
            ->json();

        $characterUrls = $response['result']['properties']["characters"];


        $characterResponses = Http::pool(function (Pool $pool) use ($characterUrls) {
            return array_map(
                fn($url) => $pool->withoutVerifying()->get($url),
                $characterUrls
            );
        });


        $characters = [];
        foreach ($characterResponses as $characterResponse) {
            if ($characterResponse->successful()) {
                $characterData = $characterResponse->json();
                $characters[] = [
                    'name' => $characterData['result']['properties']['name'],
                    'id' => $characterData['result']['uid'],
                ];
            }
        }

        return response()->json([
            "data" => [
                "title" => $response['result']['properties']["title"],
                "characters" => $characters
            ]
        ]);
    }
}
