<?php

namespace Database\Factories;

use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\PeopleController;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QueryStat>
 */
class QueryStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_at' => now(),
            'started_at' => now(),
            'ended_at' => now(),
            'endpoint' => $this->faker->randomElement([
                PeopleController::class . "@show",
                PeopleController::class . "@index",
                MovieController::class . "@show",
                MovieController::class . "@index",
            ]),
            'search_term' => $this->faker->word(),
            'resource_id' => $this->faker->numberBetween(1, 999999999),
            'total_elapsed_time_in_ms' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
