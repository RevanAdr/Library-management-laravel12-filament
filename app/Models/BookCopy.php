<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookCopy extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'copy_id';

    protected $fillable = [
        'book_id',
        'barcode',
        'status',
        'shelf_location',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class,
            'book_id',
            'book_id'
        );
    }

    public function loans(): HasMany
    {
        return $this->hasMany(
            Loan::class,
            'copy_id',
            'copy_id'
        );
    }
}
