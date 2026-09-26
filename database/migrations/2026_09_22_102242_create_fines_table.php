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
        Schema::create('fines', function (Blueprint $table) {
            $table->id('fine_id');

            $table->foreignId('loan_id')
                  ->unique()
                  ->constrained('loans', 'loan_id')
                  ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('status', 30)->default('unpaid');
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
