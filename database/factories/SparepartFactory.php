<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sparepart>
 */
class SparepartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Oli Mesin MPX', 'Oli Gardan', 'Kampas Rem Depan', 'Busi', 'Filter Udara']),
            'sku' => 'SP-' . fake()->unique()->numberBetween(1000, 9999),
            'stock' => fake()->numberBetween(10, 100),
            'sale_price' => fake()->randomElement([60000, 20000, 75000, 25000, 55000])
        ];
    }
}
