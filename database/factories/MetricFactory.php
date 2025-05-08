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
            'name' => $this->faker->word(),
            'numeric_value' => $this->faker->randomFloat(4, 0, 10000),
            'string_value' => $this->faker->word(),
            'start_at' => $this->faker->dateTime(),
            'end_at' => $this->faker->dateTime(),
        ];
    }
}
