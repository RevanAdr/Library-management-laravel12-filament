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
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id('copy_id');

            $table->foreignId('book_id')
                  ->constrained('books', 'book_id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->string('barcode', 100)->unique();
            $table->string('status', 30)->default('available');
            $table->string('shelf_location')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
