<?php

namespace App\Services\SWAAPI\Data;

class PaginatedPeopleResponseDTO
{
    /**
     * @param  PersonSummaryDTO[]  $people
     */
    public function __construct(
        public readonly array $people,
        public readonly ?int $currentPage,
        public readonly ?int $nextPage, // Page number
        public readonly int $perPage,
        public readonly ?int $totalRecords,
        public readonly int $totalPages
    ) {}
}
