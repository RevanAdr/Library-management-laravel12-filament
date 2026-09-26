<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;

    protected $primaryKey = 'author_id';

    protected $fillable = [
        'first_name',
        'last_name',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(
            Book::class,
            'book_authors',
            'author_id',
            'book_id',
            'author_id',
            'book_id'
        );
    }
}
