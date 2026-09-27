<?php

namespace App\Actions;

use App\Models\Fine;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReturnBook
{
    public function execute(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {

            // 1. Make sure this loan is still active
            if ($loan->status !== 'borrowed') {
                throw new RuntimeException(
                    'This loan has already been returned.'
                );
            }

            $returnedDate = now();

            // 2. Mark loan as returned
            $loan->update([
                'returned_date' => $returnedDate,
                'status' => 'returned',
            ]);

            // 3. Make the physical copy available again
            $loan->copy->update([
                'status' => 'available',
            ]);

            // 4. Calculate late days
            $lateDays = max(
                0,
                  $loan->due_date->startOfDay()
                     ->diffInDays($returnedDate->startOfDay())
            );

            // 5. Create fine if returned late
            if ($lateDays > 0) {
                $finePerDay = 1000;

                Fine::create([
                    'loan_id' => $loan->loan_id,
                    'amount' => $lateDays * $finePerDay,
                    'status' => 'unpaid',
                ]);
            }

            return $loan->fresh();
        });
    }
}