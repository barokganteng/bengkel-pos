<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license_plate' => strtoupper(fake()->lexify('??') . ' ' . fake()->numberBetween(1000, 9999) . ' ' . fake()->lexify('??')),
            'brand' => fake()->randomElement(['Honda', 'Yamaha', 'Suzuki', 'Kawasaki']),
            'model' => fake()->randomElement(['Vario 150', 'NMax', 'Beat', 'PCX', 'Aerox', 'Satria FU']),
            'year' => fake()->numberBetween(2015, 2024),
        ];
    }
}
