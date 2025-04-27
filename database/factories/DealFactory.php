<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $airportCodes = ['LAX', 'JFK', 'ORD', 'DFW', 'DEN'];

        return [
            'title' => fake()->sentence(3),
            'origin' => fake()->randomElement($airportCodes),
            'destination' => fake()->randomElement($airportCodes),
            'price' => fake()->randomFloat(2, 100, 1000),
            'currency' => 'USD',
            'departure_date' => fake()->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
            'return_date' => fake()->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d'),
            'provider' => 'Amadeus',
        ];
    }
}
