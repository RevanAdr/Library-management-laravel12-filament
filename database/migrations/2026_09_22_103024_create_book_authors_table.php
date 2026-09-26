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
        Schema::create('book_authors', function (Blueprint $table) {
           $table->foreignId('book_id')
                 ->constrained('books', 'book_id')
                 ->cascadeOnDelete();

           $table->foreignId('author_id')
                 ->constrained('authors', 'author_id')
                 ->cascadeOnDelete();

           $table->primary(['book_id', 'author_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_authors');
    }
};
