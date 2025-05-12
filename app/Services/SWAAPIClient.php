<?php

namespace App\Services;

use App\Services\SWAAPI\Data\MovieDetailDTO;
use App\Services\SWAAPI\Data\MovieSummaryDTO;
use App\Services\SWAAPI\Data\PaginatedPeopleResponseDTO;
use App\Services\SWAAPI\Data\PersonDetailDTO;
use App\Services\SWAAPI\Data\PersonSummaryDTO;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SWAAPIClient
{
    private const BASE_URL = 'https://www.swapi.tech/api';

    public const CACHE_PREFIX = 'swapi_cache:';

    private PendingRequest $httpClient;

    public function __construct()
    {
        $this->httpClient = Http::withoutVerifying();
    }

    /**
     * Makes a request to the SWAPI API.
     */
    private function makeRequest(string $method, string $endpoint, array $query = []): array
    {
        $cacheKey = $this->getRequestCacheSignatureKey($method, self::BASE_URL . $endpoint, $query);
        // Check if the response is remembered in the cache
        $response = Cache::remember($cacheKey, now()->addDay(), function () use ($method, $endpoint, $query) {
            return $this->httpClient->{$method}(self::BASE_URL . $endpoint, $query)->json();
        });

        return $response ?? [];
    }

    /**
     * Fetches people from SWAPI.
     */
    public function getPeople(?string $search = null, int|string|null $page = null): PaginatedPeopleResponseDTO
    {
        $queryParams = [];

        $queryParams['limit'] = 10; // SWAPI always returns 10 items per page
        if ($search !== null && $search !== '') {
            $queryParams['name'] = $search;
        }

        $currentPage = 1;
        if ($page !== null && $page !== '') {
            $queryParams['page'] = $page;
            $currentPage = (int) $page;
        }

        $responseArray = $this->makeRequest('get', '/people', $queryParams);

        $isSearching = ($search !== null && $search !== '');
        $peopleListKey = $isSearching ? 'result' : 'results'; // SWAPI returns 'result' for search, 'results' for general listing
        $peopleData = $responseArray[$peopleListKey] ?? [];

        $peopleDTOs = [];
        if (is_array($peopleData)) {
            foreach ($peopleData as $person) {
                $peopleDTOs[] = PersonSummaryDTO::fromApiResponse($person);
            }
        }

        $totalRecords = $responseArray['total_records'] ?? ($isSearching ? count($peopleDTOs) : null);
        // total_pages is not directly provided by SWAPI for /people list, calculate if possible
        $totalPages = 1;
        if ($isSearching && isset($responseArray['total_pages'])) { // Search results might have total_pages
            $totalPages = (int) $responseArray['total_pages'];
        }

        return new PaginatedPeopleResponseDTO(
            people: $peopleDTOs,
            currentPage: $currentPage,
            nextPage: self::parsePageFromUrl($responseArray['next'] ?? null),
            perPage: 10, // can't be changed, SWAPI always returns 10 items per page
            totalRecords: $totalRecords,
            totalPages: $totalPages
        );
    }

    /**
     * Fetches a person by their ID from SWAPI.
     */
    public function getPersonById(int $id): ?PersonDetailDTO
    {
        $response = $this->makeRequest('get', "/people/{$id}");
        if (empty($response['result'])) {
            return null;
        }

        // fetch the movies and attach them to the person
        $allMovieSummaries = $this->getMovies(); // There's no way to fetch movies along with a person

        $personUrl = "https://www.swapi.tech/api/people/{$id}";

        foreach ($allMovieSummaries as $movieSummary) {
            if (in_array($personUrl, $movieSummary->characterUrls)) {
                $response['result']['movies'][] = [
                    'title' => $movieSummary->title,
                    'id' => $movieSummary->id,
                ];
            }
        }

        // dd($response);
        return PersonDetailDTO::fromApiResponse($response['result']);
    }

    /**
     * Fetches movies from SWAPI.
     *
     * @return MovieSummaryDTO[]
     */
    public function getMovies(?string $search = null): array
    {
        $queryParams = [];
        if ($search !== null && $search !== '') {
            $queryParams['title'] = $search;
        }
        $responseArray = $this->makeRequest('get', '/films', $queryParams);

        // Films list is always under 'result' even without search
        $moviesData = $responseArray['result'] ?? [];
        $movieDTOs = [];
        if (is_array($moviesData)) {
            foreach ($moviesData as $movie) {
                $movieDTOs[] = MovieSummaryDTO::fromApiResponse($movie);
            }
        }

        return $movieDTOs;
    }

    public function getMovieById(int $id): ?MovieDetailDTO
    {
        $response = $this->makeRequest('get', "/films/{$id}");
        if (empty($response['result'])) {
            return null;
        }

        $response['result']['characters'] = $this->getPeopleByUrls($response['result']['properties']['characters']);

        return MovieDetailDTO::fromApiResponse($response['result']);
    }

    /**
     * Fetches multiple raw resources by their full URLs.
     *
     * @param  string[]  $urls
     */
    private function getRawResourcesByUrls(array $urls): array
    {
        $results = []; // Array to store the results
        $urlsToFetch = []; //urls that need to be fetched
        $urlCacheKeyMap = []; // Map to store the cache keys for each URL

        foreach ($urls as $url) {

            $cacheKey = $this->getRequestCacheSignatureKey('get', $url, []);
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse !== null) {
                $results[] = $cachedResponse['result'];
                continue;
            }

            $urlsToFetch[] = $url;
            $urlCacheKeyMap[$url] = $cacheKey;
        }


        $httpResponses = Http::pool(function (Pool $pool) use ($urlsToFetch) {
            $pendingPoolRequests = [];
            foreach ($urlsToFetch as $urlToFetchFromPool) {
                $pendingPoolRequests[] = $pool->as($urlToFetchFromPool)->withoutVerifying()->get($urlToFetchFromPool);
            }

            return $pendingPoolRequests;
        });

        foreach ($urlsToFetch as $fetchedUrl) {

            $response = $httpResponses[$fetchedUrl];

            if ($response && $response->successful()) {
                $responseData = $response->json();
                $results[] = $responseData['result'];
                // Cache the full responseData for future use, consistent with makeRequest.
                // Retrieve the cacheKey stored earlier for this URL.
                $cacheKeyForStorage = $urlCacheKeyMap[$fetchedUrl];
                Cache::put($cacheKeyForStorage, $responseData, now()->addDay());
            }
        }


        return $results;
    }

    /**
     * Fetches multiple people by their full URLs and returns them as PersonSummaryDTOs.
     *
     * @param  string[]  $characterUrls
     * @return PersonSummaryDTO[]
     */
    public function getPeopleByUrls(array $characterUrls): array
    {
        $rawResources = $this->getRawResourcesByUrls($characterUrls);
        $peopleDTOs = [];
        foreach ($rawResources as $resourceData) {
            $peopleDTOs[] = PersonSummaryDTO::fromApiResponse($resourceData);
        }

        return $peopleDTOs;
    }

    /**
     * Parses the page number from a URL.
     */
    public static function parsePageFromUrl(?string $url): ?int
    {
        if ($url === null) {
            return null;
        }
        $parsedUrl = parse_url($url);

        parse_str($parsedUrl['query'], $queryParams);

        return isset($queryParams['page']) ? (int) $queryParams['page'] : null;
    }

    private function getRequestCacheSignatureKey(string $method, string $endpoint, array $query = []): string
    {
        // Sort the query parameters to ensure consistent ordering
        ksort($query);

        return self::CACHE_PREFIX . ($method . $endpoint . json_encode($query));
    }
}
