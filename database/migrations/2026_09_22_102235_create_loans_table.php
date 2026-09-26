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
        Schema::create('loans', function (Blueprint $table) {
            $table->id('loan_id');

            $table->foreignId('copy_id')
                  ->constrained('book_copies', 'copy_id')
                  ->restrictOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->dateTime('borrowed_date');
            $table->dateTime('due_date');
            $table->dateTime('returned_date')->nullable();

            $table->string('status', 30)->default('borrowed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
