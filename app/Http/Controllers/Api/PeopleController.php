<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $response = Http::withoutVerifying() // just because im too lazy to set up a certificate
            ->get('https://swapi.tech/api/people', [
                'name' => $request->query('search'),
                'page' => $request->query('page'),
                'limit' => 10,
            ])
            ->json();

        $isSearching = $request->query('search') ? true : false;

        return response()->json(
            [
                "data" => collect($response[$isSearching ? 'result' : 'results'])
                    ->map(function ($person) use ($isSearching) {
                        return [
                            'name' =>  $isSearching ? $person['properties']["name"] : $person['name'],
                            'id' => $person['uid'],
                        ];
                    }),

                "current_page" => $request->query('page', 1),
                "next_page" => $this->getPageFromUrl(optional($response)['next']),
                "per_page" => 10, // Assuming the API returns 10 results per page
                "total" => optional($response)['total_records'] ?? count($response['result']),
            ]
        );
    }

    private function getPageFromUrl(string | null $url): string | null
    {
        if (is_null($url)) {
            return null;
        }
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['query'])) {
            return null;
        }

        parse_str($parsedUrl['query'], $queryParams);

        if (isset($queryParams['page'])) {
            return $queryParams['page'];
        }

        return null;
    }


    public function show(int $id)
    {
        //Now we need to fetch all the movies that this person has appeared in
        $allMovies = collect(Http::withoutVerifying() // just because im too lazy to set up a certificate
            ->get("https://swapi.tech/api/films")
            ->json("result"));
        $currentCharacterMovies = $allMovies->filter(function ($movie) use ($id) {
            return collect($movie['properties']['characters'])->contains("https://www.swapi.tech/api/people/{$id}");
        })->map(function ($movie) {
            return [
                'title' => $movie['properties']['title'],
                'id' => $movie['uid'],
            ];
        });

        $response = Http::withoutVerifying() // just because im too lazy to set up a certificate
            ->get("https://swapi.tech/api/people/{$id}")
            ->json();

        $data = $response['result']['properties'];


        return response()->json(
            [
                "data" => [
                    'birth_year' => $data['birth_year'],
                    'gender' => $data['gender'],
                    'eye_color' => $data['eye_color'],
                    'hair_color' => $data['hair_color'],
                    'height' => $data['height'],
                    'mass' => $data['mass'],
                    "name" => $data['name'],
                    "movies" => $currentCharacterMovies->toArray(),
                ]
            ]
        );
    }
}
