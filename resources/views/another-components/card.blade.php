@foreach ($books as $book)
<div class="bg-neutral-primary-soft block max-w-sm p-6 border border-default rounded-base shadow-xs">
    <a href="#">
        @if ($book->image_url)
            <img src="{{ $book->image_url }}" alt="{{ $book->title }}" class="h-full w-full object-cove rounded-base">
        @else
            <div class="flex h-full items-center justify-center text-gray-400 rounded-base">
                No Cover
            </div>
        @endif
    </a>
    <a href="#">
        <h5 class="mt-6 mb-2 text-2xl font-semibold tracking-tight text-heading">{{ $book->title }}</h5>
    </a>
    <p class="mb-6 text-body">
        {{ $book->category->name ?? 'Unknown category' }} - {{ $book->authors->pluck('name')->join(', ') }}
    </p>
    <a href="#" class="inline-flex items-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
        Read more
        <svg class="w-4 h-4 ms-1.5 rtl:rotate-180 -me-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
    </a>
    <form action="{{ route('books.borrow', $book) }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" >
             Borrow Book
        </button>
    </form>
</div>