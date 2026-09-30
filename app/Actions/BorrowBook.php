<?php

namespace App\Actions;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BorrowBook
{
    public function execute(
        User $user,
        BookCopy $copy,
    ): Loan {
        return DB::transaction(function () use ($user, $copy) {

            // 1. Re-check availability
            if ($copy->status !== 'available') {
                throw new RuntimeException(
                    'This book copy is not available.'
                );
            }

            if ($user->loans()
                ->where('status', 'borrowed')
                ->whereHas('copy', function ($query) use ($copy) {
                    $query->where('book_id', $copy->book_id);
                })
                ->exists()) {
                throw new RuntimeException(
                    'You have already borrowed this book.'
                );
            }

            // 2. Create the loan
            $loan = Loan::create([
                'copy_id' => $copy->copy_id,
                'user_id' => $user->id,
                'borrowed_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'borrowed',
            ]);

            // 3. Update copy status
            $copy->update([
                'status' => 'borrowed',
            ]);

            return $loan;
        });
    }
}