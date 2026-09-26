<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->count(20)->create();

        $categories = Category::factory()->createMany([
            ['name' => 'Fiction'],
            ['name' => 'Science Fiction'],
            ['name' => 'Mystery'],
            ['name' => 'Biography'],
            ['name' => 'History'],
            ['name' => 'Fantasy'],
            ['name' => 'Romance'],
            ['name' => 'Technology'],
            ['name' => 'Self-Help'],
        ]);

        $authors = Author::factory()->count(20)->create();

        Book::factory()
            ->count(50)
            ->state(function () use ($categories) {
                return [
                    'category_id' => $categories->random()->category_id,
                ];
            })
            ->create()
            ->each(function (Book $book) use ($authors) {

                // Attach 1–3 authors to this book
                $bookAuthors = $authors
                    ->random(rand(1, 3))
                    ->pluck('author_id');

                $book->authors()->attach($bookAuthors);

                // Create 2–3 physical copies
                BookCopy::factory()
                    ->count(rand(2, 3))
                     ->create([
                          'book_id' => $book->book_id,
                    ]);
            });
    }
}
