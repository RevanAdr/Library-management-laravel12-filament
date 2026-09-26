<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'isbn'=> fake()->unique()->isbn13(),
            'publisher' => fake()->company(),
            'publication_year' => fake()->numberBetween(1950, 2026),
            'summary' => fake()->paragraph(),
            'image_url' =>fake()->imageUrl(width: 640, height: 480, category: 'books'),
        ];
    }
}
