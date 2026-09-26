<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id('book_id');

            $table->foreignId('category_id')
                  ->constrained('categories', 'category_id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->string('title', 255);
            $table->string('isbn', 20)->unique();
            $table->string('publisher', 150)->nullable();
            $table->unsignedInteger('publication_year')->nullable();
            $table->text('summary')->nullable();
            $table->string('image_url', 500)->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
