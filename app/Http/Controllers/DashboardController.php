<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $books = Book::with([
            'category',
            'authors',
            'copies' => function ($query) {
                $query->where('status', 'available');
            },
        ])->paginate(12);

        $borrowedBookIds = $user->loans()
            ->where('status', 'borrowed')
            ->with('copy')
            ->get()
            ->pluck('copy.book_id')
            ->unique();

        return view('fine', compact(
            'books',
            'borrowedBookIds'
        ));
    }
}