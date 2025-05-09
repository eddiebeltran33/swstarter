<?php

namespace App\Services\SWAAPI\Data;

class MovieSummaryDTO
{
    /**
     * @param  string[]  $characterUrls
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly array $characterUrls
    ) {}

    public static function fromApiResponse(array $data): self
    {

        $properties = $data['properties'] ?? $data;
        $title = $properties['title'] ?? 'Unknown Title';
        $characterUrls = $properties['characters'] ?? [];
        $id = $data['uid'] ?? $data['id'];

        return new self(
            id: (int) $id,
            title: $title,
            characterUrls: $characterUrls
        );
    }
}
