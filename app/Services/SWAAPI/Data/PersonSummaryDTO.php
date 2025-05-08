<?php

namespace App\Services\SWAAPI\Data;

class PersonSummaryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}


    public static function fromApiResponse(array $data): self
    {
        // Search results for people list items under 'properties'
        // Normal list items have 'name' at the top level of the item
        // Individual resources fetched by URL (like for characters in a movie) have properties under 'properties'
        $name = $data['properties']['name'] ?? $data['name'] ?? 'Unknown Name';

        return new self(
            id: (int) $data['uid'],
            name: $name
        );
    }
}
