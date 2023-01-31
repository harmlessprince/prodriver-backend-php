<?php

namespace Database\Factories;

use App\Models\Truck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Truck>
 */
class TruckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'maker' => fake()->company(),
            'model' => fake()->companySuffix() . '-'. fake()->randomNumber(4),
            'chassis_number' => fake()->randomNumber(5),
            'registration_number' => fake()->postcode(),
            'plate_number' => fake()->citySuffix() . '-' . fake()->postcode()
        ];
    }
}
