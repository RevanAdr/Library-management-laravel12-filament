<?php

namespace Database\Factories;

use App\Models\BookCopy;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookCopy>
 */
class BookCopyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'barcode' => fake()->unique()->numerify('COPY-######'),
            'status' => 'available',
            'shelf_location' => fake()->bothify('S-##-##'),
        ];
    }

    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'borrowed',
        ]);
    }
}
