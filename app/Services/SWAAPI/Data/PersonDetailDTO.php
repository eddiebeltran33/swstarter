<?php

namespace App\Services\SWAAPI\Data;

class PersonDetailDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $birthYear,
        public readonly string $gender,
        public readonly string $eyeColor,
        public readonly string $hairColor,
        public readonly string $height,
        public readonly string $mass,
        /**
         * @param  MovieSummaryDTO[]  $movies
         */
        public readonly array $movies = [],
    ) {}

    public static function fromApiResponse(array $data): self
    {
        $properties = $data['properties'];

        return new self(
            id: (int) $data['uid'],
            name: $properties['name'],
            birthYear: $properties['birth_year'],
            gender: $properties['gender'],
            eyeColor: $properties['eye_color'],
            hairColor: $properties['hair_color'],
            height: $properties['height'],
            mass: $properties['mass'],
            movies: array_map(
                fn ($movie) => MovieSummaryDTO::fromApiResponse($movie),
                $data['movies']
            )
        );
    }
}
