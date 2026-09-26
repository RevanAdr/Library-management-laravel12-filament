<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $primaryKey = 'loan_id';

    protected $fillable = [
        'copy_id',
        'user_id',
        'borrowed_date',
        'due_date',
        'returned_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_date' => 'datetime',
            'due_date' => 'datetime',
            'returned_date' => 'datetime',
        ];
    }

     public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(
            BookCopy::class,
            'copy_id',
            'copy_id'
        );
    }

    public function fine(): HasOne
    {
        return $this->hasOne(
            Fine::class,
            'loan_id',
            'loan_id'
        );
    }
}
