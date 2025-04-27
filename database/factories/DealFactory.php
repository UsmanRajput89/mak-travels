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
        $dealTypes = ['flight', 'hotel'];
        $dealType = $this->faker->randomElement($dealTypes);
        $airportCodes = ['LAX', 'JFK', 'ORD', 'DFW', 'DEN'];

        $hotelNames = ['Hilton', 'Marriott', 'Sheraton', 'Hyatt', 'Radisson'];
        $hotelLocations = ['New York', 'Los Angeles', 'Chicago', 'Dallas', 'Denver'];

        return [
            'deal_type' => $dealType,
            'title' => $this->faker->sentence(3),
            'origin' => $dealType === 'flight' ? $this->faker->randomElement($airportCodes) : null,
            'destination' => $dealType === 'flight' ? $this->faker->randomElement($airportCodes) : null,
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'currency' => 'USD',
            'departure_date' => $dealType === 'flight' 
                ? $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d') 
                : null,  
            'return_date' => $dealType === 'flight' 
                ? $this->faker->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d') 
                : null,
            'provider' => 'Amadeus',
            'hotel_name' => $dealType === 'hotel' ? $this->faker->randomElement($hotelNames) : null,
            'hotel_location' => $dealType === 'hotel' ? $this->faker->randomElement($hotelLocations) : null,
            'deal_details' => json_encode(['raw' => $this->faker->text]),
        ];
    }
}
