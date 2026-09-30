<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Http\Controllers\BookController;

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

Route::get('/', function () {
    return view('auth/login');
});


Route::get('/dashboard', function () {
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

    return view('dashboard', compact(
        'books',
        'borrowedBookIds'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/books/{book}/borrow', [BookController::class, 'borrow'])
    ->middleware(['auth', 'verified'])
    ->name('books.borrow');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
