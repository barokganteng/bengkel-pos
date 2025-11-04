<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Ganti Oli', 'Servis Ringan', 'Tune Up', 'Servis CVT', 'Ganti Kampas Rem']),
            'price' => fake()->randomElement([50000, 75000, 150000, 80000, 35000])
        ];
    }
}
