<?php

namespace App\Http\Controllers;

use App\Actions\BorrowBook;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;

class BookController extends Controller
{
   public function borrow(Book $book): RedirectResponse
    {
    $copy = $book->copies()
        ->where('status', 'available')
        ->first();

    if (!$copy) {
        return back()->with(
            'error',
            'This book is currently unavailable.'
        );
    }

    try {
        app(BorrowBook::class)->execute(
            auth()->user(),
            $copy
        );

        return back()->with(
            'success',
            'Book borrowed successfully!'
        );

    } catch (\RuntimeException $e) {

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
    }   
}