<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'image_path' => $imageUrl, // JANGAN GUNAKAN INI
            'image_path' => 'gallery/' . fake()->image('public/storage/gallery', 600, 400, null, false),
            'caption' => fake()->sentence(4),
        ];
    }
}
