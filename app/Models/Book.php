<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'book_id';

    protected $fillable = [
        'category_id',
        'title',
        'isbn',
        'publisher',
        'publication_year',
        'summary',
        'image_url',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'category_id',
            'category_id'
        );
    }

    public function copies(): HasMany
    {
        return $this->hasMany(
            BookCopy::class,
            'book_id',
            'book_id'
        );
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            'book_authors',
            'book_id',
            'author_id',
            'book_id',
            'author_id'
        );
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(
            Reservation::class,
            'book_id',
            'book_id'
        );
    }

    
}
