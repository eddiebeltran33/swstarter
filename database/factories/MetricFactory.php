<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Metric>
 */
class MetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(
                [
                    'average_request_duration',
                    'top_movie_resources_visited',
                    'top_movie_search_terms',
                    'top_people_resources_visited',
                    'top_people_search_terms',
                    'total_errors'
                ]
            ),
        ];
    }
}
