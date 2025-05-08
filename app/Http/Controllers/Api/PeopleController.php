<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SWAAPIClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\SWAAPI\Data\PersonSummaryDTO;

class PeopleController extends Controller
{
    private SWAAPIClient $swapiClient;

    public function __construct(SWAAPIClient $swapiClient)
    {
        $this->swapiClient = $swapiClient;
    }

    public function index(Request $request): JsonResponse
    {
        $searchQuery = $request->query('search');
        $pageQuery = $request->query('page', "1");

        $paginatedPeopleResponse = $this->swapiClient->getPeople(
            $searchQuery,
            $pageQuery
        );

        $peopleListData = array_map(function (PersonSummaryDTO $personSummaryDto) {
            return [
                'name' => $personSummaryDto->name,
                'id' => $personSummaryDto->id,
            ];
        }, $paginatedPeopleResponse->people);

        return response()->json(
            [
                "data" => $peopleListData,
                "current_page" => $paginatedPeopleResponse->currentPage,
                "next_page_number" => $paginatedPeopleResponse->nextPage,
                "per_page" => $paginatedPeopleResponse->perPage,
                "total_records" => $paginatedPeopleResponse->totalRecords,
                "total_pages" => $paginatedPeopleResponse->totalPages,
            ]
        );
    }

    public function show(int $id): JsonResponse
    {
        $personDto = $this->swapiClient->getPersonById($id);

        if (!$personDto) {
            return response()->json(['message' => 'Person not found'], 404);
        }

        return response()->json(
            [
                "data" => [
                    'id' => $personDto->id,
                    'name' => $personDto->name,
                    'birth_year' => $personDto->birthYear,
                    'gender' => $personDto->gender,
                    'eye_color' => $personDto->eyeColor,
                    'hair_color' => $personDto->hairColor,
                    'height' => $personDto->height,
                    'mass' => $personDto->mass,
                    "movies" => $personDto->movies,
                ]
            ]
        );
    }
}
