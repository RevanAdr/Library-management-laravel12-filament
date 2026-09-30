<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Cendekia</title>
    
    <!-- Script Tailwind CDN (Pastikan temanmu menyesuaikan ini dengan Vite jika mereka sudah setup Node.js) -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
     @vite('resources/css/app.css')
</head>
<body class="bg-linear-to-br from-[#f6f3f0] to-[#eaddd7] min-h-screen flex items-center justify-center p-4 md:p-10 font-sans">

    <!-- Kontainer utama (Kartu Putih Raksasa) -->
    <div class="max-w-[1200px] w-full mx-auto bg-white rounded-[40px] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.08)] p-10 md:p-14 relative overflow-hidden">

        {{-- Success notification --}}
@if (session('success'))
    <div
        id="notification"
        class="fixed top-6 right-6 z-50 w-80 rounded-xl border border-gray-200 bg-white p-4 shadow-lg"
    >
        <div class="flex items-start gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600">
                ✓
            </div>

            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">
                    Success
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    {{ session('success') }}
                </p>
            </div>

            <button
                onclick="document.getElementById('notification').remove()"
                class="text-lg text-gray-400 hover:text-gray-600"
            >
                ×
            </button>
        </div>
    </div>
@endif


{{-- Error notification --}}
@if (session('error'))
    <div
        id="notification"
        class="fixed top-6 right-6 z-50 w-80 rounded-xl border border-gray-200 bg-white p-4 shadow-lg"
    >
        <div class="flex items-start gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                !
            </div>

            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">
                    Error
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    {{ session('error') }}
                </p>
            </div>

            <button
                onclick="document.getElementById('notification').remove()"
                class="text-lg text-gray-400 hover:text-gray-600"
            >
                ×
            </button>
        </div>
    </div>
@endif


<script>
    setTimeout(() => {
        const notification = document.getElementById('notification');

        if (notification) {
            notification.remove();
        }
    }, 4000);
</script>

        <!-- Navigasi -->
        <nav class="flex justify-between items-center mb-20 relative z-20">
            <div class="text-2xl font-extrabold text-gray-900 tracking-tight">Cendekia</div>
            
            <div class="hidden md:flex space-x-10 text-xs font-semibold text-gray-400">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('member')" class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">
                    {{ __('Dashboard') }}
                </x-nav-link>
                <x-nav-link :href="route('profile.edit')" class="block py-2 px-3 text-heading rounded hover:bg-neutral-tertiary md:hover:bg-transparent md:border-0 md:hover:text-fg-brand md:p-0 md:dark:hover:bg-transparent">
                    {{ __('Profile') }}
                </x-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </div>
        </nav>

        <!-- Bagian Hero -->
        <div class="flex flex-col md:flex-row items-center relative z-20">
            
            <!-- Kiri: Tipografi -->
            <div class="md:w-[55%] z-30">
                <h1 class="text-[3.5rem] md:text-[4.5rem] font-bold text-gray-900 leading-[1.05] tracking-tight mb-6">
                    Find the book <br> you're looking for <br> easier to read.
                </h1>
                <p class="text-gray-400 text-sm mb-12">The most appropriate book site to reach books</p>

                <!-- Search Bar -->
                <div class="flex items-center bg-white shadow-[0_10px_40px_rgb(0,0,0,0.12)] rounded-full p-2 max-w-lg border border-gray-50">
                    <input type="text" placeholder="Find your favorite book here..." class="grow px-6 py-2 outline-none text-sm text-gray-500 bg-transparent border-none focus:ring-0">
                    <button class="bg-gray-800 hover:bg-gray-950 text-white px-8 py-3.5 rounded-full text-sm font-semibold transition shadow-md">
                        Search
                    </button>
                </div>
                
            </div>

            <!-- Kanan: Area Gambar 3D & Ikon Melayang -->
            <div class="md:w-[45%] relative mt-16 md:mt-0 flex justify-center items-center h-[400px]">
                
                <!-- Gambar Tumpukan Buku Langsung dari Internet -->
                <div class="relative z-10 w-[110%] flex items-center justify-center transform hover:scale-105 transition duration-500">
                    <img src="{{ asset('images/book.png') }}" alt="Tumpukan Buku" class="object-cover rounded-[40px] shadow-[0_20px_50px_rgba(0,0,0,0.15)] h-[300px] w-full">
                </div>
            </div>
        </div>

        <!-- Bagian Footer (3 Kolom) -->
        <div class="mt-32 pt-8 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-10 relative z-20">
            <div class="pr-6">
                <span class="text-gray-300 font-bold text-xs tracking-wider mb-3 block">New Arrived</span>
                <h3 class="font-extrabold text-xl text-gray-900 leading-tight">Have you choosen a <br> good book?</h3>
            </div>
            
            <div class="pr-6 md:border-l border-gray-100 md:pl-10">
                <span class="text-gray-300 font-semibold text-xs mb-3 block">Blog - 12/21</span>
                <h4 class="font-bold text-gray-800 text-sm leading-relaxed">Wher do you want to go <br> today? find it in a book</h4>
            </div>
            
            <div class="md:border-l border-gray-100 md:pl-10">
                <span class="text-gray-300 font-semibold text-xs mb-3 block">Blog - 12/21</span>
                <h4 class="font-bold text-gray-800 text-sm leading-relaxed">Give the gift of love - <br> read to someone</h4>
            </div>
        </div>

        <!-- Efek Latar Belakang Lingkaran (Background Element) -->
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] bg-linear-to-br from-[#fbfbfb] to-[#f4f4f4] rounded-full z-0 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[400px] h-[400px] bg-linear-to-tl from-[#fdfdfd] to-[#f0f0f0] rounded-full z-0 opacity-50 blur-2xl"></div>

        <div class="py-12">
    <div class="mx-auto max-w-7xl px-6">

        <h2 class="mb-6 text-2xl font-bold text-gray-900">
            Library Books
        </h2>

        {{-- Book Grid --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

            @foreach ($books as $book)

                <div class="bg-neutral-primary-soft block max-w-sm p-6 border border-default rounded-base shadow-xs">

                    {{-- Book Image --}}
                    <a href="#">
                        @if ($book->image_url)
                            <img
                                class="rounded-base w-full h-64 object-cover"
                                src="{{ $book->image_url }}"
                                loading="lazy"
                                alt="{{ $book->title }}"
                            >
                        @else
                            <div class="flex items-center justify-center w-full h-64 rounded-base bg-gray-100 text-gray-400">
                                No Cover
                            </div>
                        @endif
                    </a>

                    {{-- Title --}}
                    <a href="#">
                        <h5 class="mt-6 mb-2 text-2xl font-semibold tracking-tight text-heading">
                            {{ $book->title }}
                        </h5>
                    </a>

                    {{-- Author --}}
                    <p class="mb-2 text-body">
                        {{ $book->authors->pluck('name')->join(', ') }}
                    </p>

                    {{-- Category --}}
                    <p class="mb-6 text-body">
                        {{ $book->category->name ?? 'Unknown Category' }}
                    </p>

                    {{-- Borrow Button --}}
                @if ($borrowedBookIds->contains($book->book_id))

                        <button disabled class="w-full rounded-lg bg-gray-300 px-5 py-2.5 text-sm font-medium text-gray-500 cursor-not-allowed">
                             Already Borrowed
                        </button>

                @elseif ($book->copies->isNotEmpty())

                    <form action="{{ route('books.borrow', $book) }}" method="POST">
                    @csrf
                        <button type="submit" class="w-full rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-black hover:bg-blue-700">
                            Borrow Book
                        </button>
                    </form>

                @else

                    <button disabled class="w-full rounded-lg bg-gray-300 px-5 py-2.5 text-sm font-medium text-gray-500 cursor-not-allowed">
                         Unavailable
                    </button>

                @endif

                </div>

            @endforeach

        </div>
        <div class="mt-8">
            {{ $books->links() }}
        </div>

    </div>
</div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>
</html>