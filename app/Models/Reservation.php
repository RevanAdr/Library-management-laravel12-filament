<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'book_id',
        'user_id',
        'reservation_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'datetime',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class,
            'book_id',
            'book_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }
}
