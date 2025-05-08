<?php

namespace App\Services\SWAAPI\Data;

class MovieDetailDTO
{
    /**
     * @param string[] $characterUrls
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $openingCrawl,
        public readonly string $director,
        public readonly string $producer,
        public readonly string $releaseDate,
        public readonly array $characterUrls,
        /**
         *
         * @var PersonSummaryDTO[]
         */
        public readonly array $characters = [],
    ) {}

    public static function fromApiResponse(array $data): self
    {
        $properties = $data['properties'];
        return new self(
            id: (int) $data['uid'],
            title: $properties['title'],
            openingCrawl: $properties['opening_crawl'],
            director: $properties['director'],
            producer: $properties['producer'],
            releaseDate: $properties['release_date'],
            characterUrls: $properties['characters'],
            characters: $data['characters'] ?? []
        );
    }
}
