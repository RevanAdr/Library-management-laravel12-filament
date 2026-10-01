@php
    // HERO: isi dengan foto perpustakaan, contoh: asset('images/perpustakaan.jpg'). Kosong = pakai rak buku dari CSS
    $heroImage = '';

    $q = trim((string) request('q'));
    $penulis = trim((string) request('penulis'));
    $kat = request('kategori');
    $urut = request('urut', 'terbaru');

    $allBooks = \App\Models\Book::with(['category', 'authors'])->latest()->get();

    $books = \App\Models\Book::with(['category', 'authors'])
        ->when($q, fn ($x, $s) => $x->where('title', 'like', "%{$s}%"))
        ->when($penulis, fn ($x, $s) => $x->whereHas('authors', fn ($a) => $a->where(
            fn ($w) => $w->where('first_name', 'like', "%{$s}%")->orWhere('last_name', 'like', "%{$s}%")
        )))
        ->when($kat, fn ($x, $s) => $x->whereHas('category', fn ($c) => $c->where('name', $s)))
        ->when($urut === 'judul', fn ($x) => $x->orderBy('title'),
            fn ($x) => $urut === 'tahun' ? $x->orderByDesc('publication_year') : $x->latest())
        ->get();

    try { $categories = \App\Models\Category::orderBy('name')->pluck('name')->unique()->values(); }
    catch (\Throwable $e) { $categories = $allBooks->pluck('category.name')->filter()->unique()->values(); }

    $totalCategories = $categories->count();
    try { $totalAuthors = \App\Models\Author::count(); } catch (\Throwable $e) { $totalAuthors = $allBooks->pluck('authors')->flatten()->unique('id')->count(); }

    $totalBooks = \App\Models\Book::count();
    $newest = $allBooks->take(4);

    // GANTI 'loans' dengan nama relasi peminjaman di model Book
    try {
        $bestSellers = \App\Models\Book::with(['category', 'authors'])->withCount('loans')->orderByDesc('loans_count')->take(4)->get();
    } catch (\Throwable $e) { $bestSellers = $allBooks->take(4); }
    try { $totalBorrowed = \App\Models\Loan::count(); } catch (\Throwable $e) { $totalBorrowed = $bestSellers->sum('loans_count'); }

    $stats = [
        ['n' => $totalBooks, 'label' => 'Buku siap dipinjam', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['n' => $totalCategories, 'label' => 'Kategori', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
        ['n' => $totalAuthors, 'label' => 'Penulis', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];

    // HISTORY (sesuaikan model/kolom)
    $user = auth()->user();
    $history = $user
        ? \App\Models\Loan::with('copy.book')
            ->where('user_id', $user->id)
            ->latest('borrowed_date')
            ->get()
        : collect();

    $historyActive = $history
        ->where('status', 'borrowed')
        ->count();

    $historyReturned = $history
        ->where('status', 'returned')
        ->count();

    $fmtDate = fn ($date) =>
        $date?->translatedFormat('d M Y') ?? '-';

    $loanStatus = function ($loan) {
        if ($loan->status === 'returned') {
            return [
                'Sudah dikembalikan',
                'bg-emerald-100 text-emerald-800'
            ];
        }

        if (
            $loan->status === 'borrowed' &&
            $loan->due_date &&
            \Carbon\Carbon::parse($loan->due_date)->isPast()
        ) {
            return [
                'Terlambat',
                'bg-red-100 text-red-800'
            ];
        }

        return [
            'Belum dikembalikan',
            'bg-amber-100 text-amber-800'
        ];
    };

    $userName = $user->name ?? 'Tamu';
    $nick = data_get($user, 'nickname') ?: $userName;
    $userPhoto = data_get($user, 'photo') ? asset('storage/'.data_get($user, 'photo')) : (data_get($user, 'profile_photo_url') ?: data_get($user, 'avatar'));
    $userInit = e(strtoupper(mb_substr($nick, 0, 1)));

    // Kartu buku, klik = buka detail
    // SESUAIKAN nama kolom: isbn, synopsis (atau description), stock (atau quantity)
    $card = function ($book, $badge = null) {
        $initial = e(strtoupper(mb_substr($book->title, 0, 1)));
        $title = e($book->title);
        $category = e($book->category->name ?? '-');
        $authorsTxt = $book->authors->map(fn ($a) => $a->first_name.' '.$a->last_name)->join(', ') ?: 'Penulis tidak diketahui';
        $authors = e($authorsTxt);

        $payload = e(json_encode([
            'id' => $book->book_id,
            'title' => $book->title,
            'authors' => $authorsTxt,
            'category' => $book->category->name ?? '-',
            'year' => $book->publication_year,
            'isbn' => $book->isbn ?? '-',
            'synopsis' => $book->summary ?? 'Belum ada sinopsis.',
            'image' => $book->image_url,
            'stock' => $book->copies->count(),
        ]));

        $img = $book->image_url
            ? '<img src="'.e($book->image_url).'" alt="'.$title.'" class="absolute inset-0 h-full w-full object-cover" onerror="this.style.display=\'none\'">' : '';
        $badgeHtml = !empty($badge)
            ? '<span class="absolute top-3 left-3 bg-espresso text-cream text-[11px] font-bold px-3 py-1 rounded-full shadow">'.e($badge).'</span>' : '';
        $year = $book->publication_year ? '<p class="text-espresso/40 text-xs mt-1">'.e($book->publication_year).'</p>' : '';

        return <<<HTML
        <button type="button" data-book="{$payload}" class="book-card text-left group bg-white rounded-2xl shadow-[0_10px_30px_rgba(107,74,47,0.10)] border border-sand/60 overflow-hidden hover:-translate-y-1.5 hover:shadow-[0_18px_40px_rgba(107,74,47,0.18)] hover:border-gold transition duration-300">
            <div class="relative h-44 w-full bg-gradient-to-br from-honey to-tan flex items-center justify-center">
                <span class="text-espresso/70 text-5xl font-serif font-bold">{$initial}</span>
                {$img}
                {$badgeHtml}
            </div>
            <div class="p-4">
                <span class="text-coffee text-[11px] font-bold uppercase tracking-wider">{$category}</span>
                <h3 class="font-serif font-semibold text-espresso text-base mt-1 leading-snug">{$title}</h3>
                <p class="text-espresso/60 text-xs mt-1">{$authors}</p>
                {$year}
            </div>
        </button>
        HTML;
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Cendekia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Montserrat','sans-serif'], serif: ['"Playfair Display"','serif'] },
            colors: {
                cream:'#fdf6e3', butter:'#f8e8b0', honey:'#f0cf7a', sand:'#e8d3a8', tan:'#d3ac78',
                coffee:'#a06a3b', mocha:'#7a5236', espresso:'#3f2b1d', gold:'#e2b64f',
            },
        } } }
    </script>
    <style>
        body { background:
            radial-gradient(900px 500px at 100% -10%, rgba(240,207,122,.35), transparent 60%),
            radial-gradient(700px 500px at -10% 30%, rgba(211,172,120,.22), transparent 60%), #fdf6e3; }

        .view { display:none; opacity:0; transform:translateY(14px); transition:opacity .45s ease, transform .45s ease; }
        .view.active { display:block; }
        .view.show { opacity:1; transform:none; }

        .nav-btn { position:relative; padding:.5rem .25rem; transition:color .3s; }
        .nav-btn::after { content:''; position:absolute; left:0; right:0; bottom:0; height:2px; background:#a06a3b;
                          transform:scaleX(0); transform-origin:left; transition:transform .35s ease; }
        .nav-btn:hover { color:#a06a3b; }
        .nav-btn.on { color:#3f2b1d; }
        .nav-btn.on::after { transform:scaleX(1); }

        .title-line { position:relative; padding-bottom:.6rem; }
        .title-line::after { content:''; position:absolute; left:0; bottom:0; width:44px; height:3px; border-radius:9999px;
                             background:linear-gradient(90deg,#e2b64f,#a06a3b); }

        .chip { display:inline-block; white-space:nowrap; padding:.45rem 1rem; border-radius:9999px; font-size:.75rem; font-weight:600;
                color:#7a5236; background:#fff; border:1px solid rgba(232,211,168,.9); transition:all .25s; }
        .chip:hover { border-color:#e2b64f; color:#a06a3b; }
        .chip-on { background:#3f2b1d; color:#fdf6e3; border-color:#3f2b1d; }
        .chip-on:hover { color:#fdf6e3; border-color:#3f2b1d; }

        .scroll-x { scrollbar-width:none; }
        .scroll-x::-webkit-scrollbar { display:none; }

        #settingsMenu { opacity:0; transform:translateY(6px) scale(.98); pointer-events:none; transition:opacity .2s ease, transform .2s ease; }
        #settingsMenu.open { opacity:1; transform:none; pointer-events:auto; }

        #profileModal { opacity:0; pointer-events:none; transition:opacity .3s ease; }
        #profileModal.open { opacity:1; pointer-events:auto; }
        #profileModal .panel { transform:translateY(20px); transition:transform .3s ease; }
        #profileModal.open .panel { transform:none; }
        /* Modal detail buku */
        #bookModal { opacity:0; pointer-events:none; transition:opacity .3s ease; }
        #bookModal.open { opacity:1; pointer-events:auto; }
        #bookModal .panel { transform:translateY(20px) scale(.98); transition:transform .3s ease; }
        #bookModal.open .panel { transform:none; }

        /* HERO: latar rak buku dari CSS */
        .shelf-bg {
            background-image:
                linear-gradient(to bottom, transparent calc(100% - 10px), rgba(63,43,29,.85) calc(100% - 10px)),
                repeating-linear-gradient(90deg,
                    #7a5236 0 16px, #d3ac78 16px 26px, #3f2b1d 26px 44px, #a06a3b 44px 56px,
                    #e2b64f 56px 64px, #7a5236 64px 84px, #f0cf7a 84px 94px, #a06a3b 94px 112px);
            background-size: 100% 120px, 100% 120px;
        }
        @keyframes floaty { 0%,100% { transform: translateY(0) rotate(var(--r,0deg)); } 50% { transform: translateY(-12px) rotate(var(--r,0deg)); } }
        .floaty { animation: floaty 5s ease-in-out infinite; }
        .floaty.d1 { animation-delay: -1.5s; } .floaty.d2 { animation-delay: -3s; }

        /* HERO: tumpukan buku */
        .stack-book { position: relative; height: 54px; border-radius: 6px 10px 10px 6px; display:flex; align-items:center; justify-content:center;
            font-family: 'Playfair Display', serif; font-weight: 600; font-size: 13px; letter-spacing: .04em; color: #3f2b1d;
            box-shadow: 0 10px 18px -8px rgba(63,43,29,.5), inset 0 -4px 0 rgba(0,0,0,.08); transition: transform .4s ease; }
        .stack-book::before { content:''; position:absolute; left:10px; top:0; bottom:0; width:3px; background:rgba(255,255,255,.35); }
        .stack-book::after { content:''; position:absolute; right:0; top:6px; bottom:6px; width:6px; background:repeating-linear-gradient(180deg,#fff 0 2px,#f3e6c4 2px 4px); border-radius:0 6px 6px 0; }
        #heroStack:hover .stack-book:nth-child(odd)  { transform: translateX(10px); }
        #heroStack:hover .stack-book:nth-child(even) { transform: translateX(-10px); }

        @media (prefers-reduced-motion: reduce) { .view, .nav-btn::after, #settingsMenu, #bookModal, #bookModal .panel, .stack-book { transition:none; } .floaty { animation:none; } }
    </style>
</head>
<body class="text-espresso font-sans antialiased min-h-screen flex flex-col">

    <header class="sticky top-0 z-50 bg-cream/85 backdrop-blur border-b border-sand">
        <nav class="max-w-[1200px] mx-auto flex items-center justify-between px-6 md:px-10 py-4">
            <button data-go="home" class="font-serif text-2xl font-bold tracking-wide text-coffee">Cendekia</button>
            <div class="flex items-center gap-4 md:gap-10 text-[11px] md:text-xs font-semibold tracking-[0.15em] text-mocha/60">
                <button data-go="home" class="nav-btn">Home</button>
                <button data-go="catalog" class="nav-btn">Catalog</button>
                
                <button data-go="history" class="nav-btn">History</button>
            </div>
        </nav>
    </header>

    <main class="flex-1 w-full max-w-[1200px] mx-auto px-6 md:px-10 py-10 md:py-14">
        @if (session('status'))
            <div class="mb-6 rounded-2xl bg-emerald-100 text-emerald-800 text-sm font-semibold px-5 py-3">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-red-100 text-red-800 text-sm font-semibold px-5 py-3">{{ $errors->first() }}</div>
        @endif

        <!-- HOME -->
        <section id="home" class="view">
            <!-- HERO baru -->
            <div class="relative">
                <div class="relative overflow-hidden rounded-[32px] shadow-[0_30px_70px_-20px_rgba(63,43,29,0.5)] min-h-[520px] md:min-h-[560px] bg-espresso">
                    <!-- Latar perpustakaan -->
                    <div class="absolute inset-0 shelf-bg opacity-90"></div>
                    @if ($heroImage)
                        <img src="{{ $heroImage }}" alt="" class="absolute inset-0 h-full w-full object-cover" onerror="this.style.display='none'">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-r from-cream via-cream/90 to-cream/20 md:via-cream/75"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-espresso/30 via-transparent to-transparent"></div>
                    <div class="absolute -top-20 right-10 w-72 h-72 rounded-full bg-honey/50 blur-3xl"></div>

                    <div class="relative grid md:grid-cols-[1.1fr_1fr] gap-6 items-center px-8 md:px-14 pt-12 pb-12 md:pb-14">
                        <!-- Kiri: teks -->
                        <div>
                            <span class="inline-block text-[11px] font-bold tracking-[0.2em] uppercase text-coffee bg-white/70 backdrop-blur border border-honey px-3 py-1 rounded-full mb-5">Perpustakaan Digital</span>
                            <h1 class="font-serif text-5xl md:text-7xl font-semibold leading-[1.05]">
                                Temukan <span class="italic text-coffee">dunia</span><br>di setiap halaman.
                            </h1>
                            <p class="text-sm md:text-base leading-relaxed text-espresso/70 max-w-md mt-6 mb-8">
                                Jelajahi koleksi Cendekia, temukan buku yang kamu cari, dan pinjam dengan mudah.
                            </p>

                            <form method="GET" action="/" class="flex items-center bg-white rounded-full p-2 max-w-lg border border-sand shadow-[0_15px_35px_-10px_rgba(160,106,59,0.4)] focus-within:border-gold transition">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari buku favoritmu di sini..."
                                       class="flex-grow px-5 py-2 outline-none text-sm placeholder:text-espresso/40 bg-transparent border-none focus:ring-0">
                                <button type="submit" class="bg-espresso hover:bg-coffee text-cream px-7 py-3 rounded-full text-sm font-semibold transition shadow-md">Cari</button>
                            </form>

                            <div class="flex flex-wrap gap-3 mt-6">
                                <button data-go="catalog" class="bg-espresso hover:bg-coffee text-cream text-xs font-bold tracking-wider px-6 py-3 rounded-full transition">JELAJAHI KOLEKSI &rarr;</button>
                                <button data-go="history" class="bg-white/70 backdrop-blur border border-espresso/30 hover:border-gold text-espresso text-xs font-bold tracking-wider px-6 py-3 rounded-full transition">RIWAYAT SAYA</button>
                            </div>
                        </div>

                        <!-- Kanan: tumpukan buku + ikon melayang -->
                        <div class="relative hidden md:flex justify-center items-end h-[420px]">

                            <div id="heroStack" class="floaty d1 relative w-[300px]" style="--r:0deg">
                                <div class="stack-book w-[210px] mx-auto bg-espresso !text-gold rotate-[-2deg]">Filosofi</div>
                                <div class="stack-book w-[260px] mx-auto bg-honey rotate-[2deg]">Sastra</div>
                                <div class="stack-book w-[240px] mx-auto bg-coffee !text-cream rotate-[-1deg]">Sejarah</div>
                                <div class="stack-book w-[280px] mx-auto bg-butter rotate-[3deg]">Sains</div>
                                <div class="stack-book w-[250px] mx-auto bg-mocha !text-cream rotate-[-2deg]">Novel</div>
                                <div class="stack-book w-[290px] mx-auto bg-sand rotate-[1deg]">Cendekia</div>
                            </div>

                            <div class="floaty absolute top-24 -right-2 w-28 h-28 rounded-full bg-espresso text-cream shadow-2xl flex flex-col items-center justify-center text-center ring-4 ring-gold/60" style="--r:6deg">
                                <span class="font-serif text-lg font-semibold leading-tight">Koleksi<br>Baru</span>
                                <span class="text-[10px] tracking-wider text-gold mt-1">Minggu ini</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu statistik (data dari database) -->
                <div class="relative mt-10 grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @foreach ($stats as $st)
                        <div class="group relative overflow-hidden bg-white rounded-3xl border border-sand/60 p-5 md:p-6 shadow-[0_20px_45px_-18px_rgba(63,43,29,0.45)] hover:-translate-y-1.5 hover:border-gold transition duration-300">
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-gold via-honey to-coffee"></div>
                            <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-honey/25 group-hover:text-honey/40 group-hover:rotate-6 transition duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $st['icon'] }}"/></svg>
                            <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-br from-honey to-tan flex items-center justify-center shadow-inner mb-4">
                                <svg class="w-5 h-5 text-espresso" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $st['icon'] }}"/></svg>
                            </div>
                            <p class="relative font-serif text-4xl md:text-5xl font-bold text-espresso leading-none" data-count="{{ (int) $st['n'] }}">{{ (int) $st['n'] }}</p>
                            <p class="relative text-xs font-semibold tracking-wide text-coffee mt-2">{{ $st['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-16">
                <div class="flex items-end justify-between mb-6">
                    <h2 class="title-line font-serif text-3xl font-semibold">Buku terbaru</h2>
                    <button data-go="catalog" class="text-xs font-semibold text-coffee hover:text-espresso transition">Lihat semua</button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($newest as $book) {!! $card($book, 'Baru') !!} @endforeach
                </div>
            </div>

            <div class="mt-16">
                <h2 class="title-line font-serif text-3xl font-semibold mb-6">Paling sering dipinjam</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($bestSellers as $i => $book) {!! $card($book, '#'.($i + 1)) !!} @endforeach
                </div>
            </div>
        </section>

        <!-- CATALOG -->
        <section id="catalog" class="view">
            <div class="flex items-end justify-between mb-6">
                <h2 class="title-line font-serif text-3xl font-semibold">@if ($q) Hasil untuk "{{ $q }}" @else Catalog @endif</h2>
                <span class="text-xs font-semibold text-espresso/50">{{ $books->count() }} buku</span>
            </div>

            <form method="GET" action="/" class="bg-white rounded-2xl border border-sand/60 shadow-[0_10px_30px_rgba(107,74,47,0.08)] p-4 md:p-5 mb-5">
                @if ($kat) <input type="hidden" name="kategori" value="{{ $kat }}"> @endif
                <div class="grid md:grid-cols-[1.3fr_1fr_0.7fr_auto] gap-3">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul buku..." class="w-full rounded-xl border border-sand bg-cream/60 px-4 py-2.5 text-sm outline-none focus:border-gold focus:ring-0 placeholder:text-espresso/40">
                    <input type="text" name="penulis" value="{{ $penulis }}" placeholder="Nama penulis..." class="w-full rounded-xl border border-sand bg-cream/60 px-4 py-2.5 text-sm outline-none focus:border-gold focus:ring-0 placeholder:text-espresso/40">
                    <select name="urut" class="w-full rounded-xl border border-sand bg-cream/60 px-4 py-2.5 text-sm outline-none focus:border-gold focus:ring-0">
                        <option value="terbaru" @selected($urut === 'terbaru')>Terbaru</option>
                        <option value="judul" @selected($urut === 'judul')>Judul A–Z</option>
                        <option value="tahun" @selected($urut === 'tahun')>Tahun terbit</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-espresso hover:bg-coffee text-cream px-6 py-2.5 rounded-xl text-sm font-semibold transition">Cari</button>
                        <a href="/#catalog" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-coffee border border-sand hover:border-gold transition">Reset</a>
                    </div>
                </div>
            </form>

            <div class="scroll-x flex gap-2 overflow-x-auto pb-2 mb-8">
                <a href="{{ request()->fullUrlWithQuery(['kategori' => null]) }}" class="chip {{ !$kat ? 'chip-on' : '' }}">Semua</a>
                @foreach ($categories as $c)
                    <a href="{{ request()->fullUrlWithQuery(['kategori' => $c]) }}" class="chip {{ $kat === $c ? 'chip-on' : '' }}">{{ $c }}</a>
                @endforeach
            </div>

            @if ($books->isEmpty())
                <p class="text-espresso/50 text-sm">Buku tidak ditemukan. Coba kata kunci atau kategori lain.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($books as $book) {!! $card($book) !!} @endforeach
                </div>
            @endif
        </section>

        <!-- HISTORY -->
        <section id="history" class="view">
            <h2 class="title-line font-serif text-3xl font-semibold mb-8">History</h2>
            @if (!$user)
                <div class="bg-white rounded-2xl border border-sand/60 p-8 text-center">
                    <p class="text-sm text-espresso/60">Masuk dulu untuk melihat riwayat peminjamanmu.</p>
                    <a href="{{ url('/login') }}" class="inline-block mt-4 bg-espresso text-cream text-xs font-bold px-6 py-2.5 rounded-full">Masuk</a>
                </div>
            @else
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-2xl border border-sand/60 p-5"><p class="font-serif text-3xl font-semibold">{{ $history->count() }}</p><p class="text-xs text-espresso/60 mt-1">Total dipinjam</p></div>
                    <div class="bg-white rounded-2xl border border-sand/60 p-5"><p class="font-serif text-3xl font-semibold text-coffee">{{ $historyActive }}</p><p class="text-xs text-espresso/60 mt-1">Belum dikembalikan</p></div>
                    <div class="bg-white rounded-2xl border border-sand/60 p-5"><p class="font-serif text-3xl font-semibold">{{ $historyReturned }}</p><p class="text-xs text-espresso/60 mt-1">Sudah dikembalikan</p></div>
                </div>
                @if ($history->isEmpty())
                    <p class="text-espresso/50 text-sm">Kamu belum pernah meminjam buku.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($history as $loan)
                            @php [$statusText, $statusClass] = $loanStatus($loan); $book = $loan->copy->book; @endphp
                            <div class="flex items-center gap-4 bg-white rounded-2xl border border-sand/60 p-4">
                                <div class="shrink-0 w-12 h-16 rounded-lg bg-gradient-to-br from-honey to-tan flex items-center justify-center">
                                    <span class="font-serif text-xl font-bold text-espresso/70">{{ strtoupper(mb_substr($book->title, 0, 1)) }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-serif font-semibold text-base truncate">{{ $book->title }}</h3>
                                    <p class="text-xs text-espresso/60 mt-1">
                                        Dipinjam {{ $fmtDate($loan->borrowed_date) }} · Batas {{ $fmtDate($loan->due_date) }}
                                        @if ($loan->returned_date) · Kembali {{ $fmtDate($loan->returned_date) }} @endif
                                    </p>
                                </div>
                                <span class="shrink-0 text-[11px] font-bold px-3 py-1 rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </section>
    </main>

    <!-- Footer: selalu di paling bawah -->
    <footer class="mt-auto bg-butter/70 border-t border-sand text-espresso/60 py-8 text-center text-xs">
        &copy; {{ date('Y') }} <span class="font-semibold text-coffee">Perpustakaan Cendekia</span> · Semua hak dilindungi
    </footer>

    <!-- Modal detail buku -->
    <div id="bookModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-espresso/40 backdrop-blur-sm">
        <div class="panel relative w-full max-w-3xl max-h-[90vh] overflow-y-auto bg-cream rounded-[28px] shadow-2xl p-6 md:p-10">
            <button type="button" id="modalClose" aria-label="Tutup" class="absolute top-4 right-5 text-2xl text-espresso/50 hover:text-espresso">&times;</button>
            <div class="grid md:grid-cols-[220px_1fr] gap-8">
                <div class="relative mx-auto w-[200px] md:w-full aspect-[2/3] rounded-lg overflow-hidden bg-gradient-to-br from-honey to-tan shadow-[0_25px_40px_-10px_rgba(63,43,29,0.45)] flex items-center justify-center">
                    <span id="mInit" class="font-serif text-7xl font-bold text-espresso/60"></span>
                    <img id="mImg" alt="" class="absolute inset-0 w-full h-full object-cover hidden">
                </div>
                <div>
                    <span id="mCat" class="text-coffee text-[11px] font-bold uppercase tracking-wider"></span>
                    <h3 id="mTitle" class="font-serif text-3xl md:text-4xl font-semibold leading-tight mt-1"></h3>
                    <p id="mAuthor" class="text-sm font-semibold mt-3"></p>
                    <p id="mYear" class="text-xs text-espresso/50 mt-1"></p>

                    <div class="mt-5">
                        <p class="text-xs font-bold tracking-wider uppercase text-coffee">Sinopsis</p>
                        <p id="mSyn" class="text-sm leading-relaxed text-espresso/70 mt-2"></p>
                    </div>
                    <div class="mt-5 text-xs">
                        <span class="font-bold tracking-wider uppercase text-coffee">ISBN</span>
                        <span id="mIsbn" class="ml-2 text-espresso/70"></span>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <span id="mStock" class="text-xs font-bold px-3 py-1.5 rounded-full"></span>
                    </div>
                    <div id="mAction" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal profil -->
    <div id="profileModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-espresso/40 backdrop-blur-sm">
        <div class="panel relative w-full max-w-md max-h-[90vh] overflow-y-auto bg-cream rounded-[28px] shadow-2xl p-6 md:p-8">
            <button type="button" id="profileClose" aria-label="Tutup" class="absolute top-4 right-5 text-2xl text-espresso/50 hover:text-espresso">&times;</button>
            <h3 class="font-serif text-2xl font-semibold mb-5">Profil saya</h3>
            @if ($user)
                <form method="POST" action="{{ url('/profile/update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="flex flex-col items-center gap-3">
                        <div class="relative w-24 h-24 rounded-full ring-4 ring-honey overflow-hidden bg-gradient-to-br from-honey to-tan flex items-center justify-center">
                            <span class="font-serif text-4xl font-bold text-espresso/70">{!! $userInit !!}</span>
                            <img id="photoPrev" src="{{ $userPhoto }}" alt="" class="absolute inset-0 w-full h-full object-cover {{ $userPhoto ? '' : 'hidden' }}">
                        </div>
                        <label class="cursor-pointer text-xs font-semibold text-coffee border border-sand rounded-full px-4 py-1.5 hover:border-gold transition">Ganti foto
                            <input id="photoInput" type="file" name="photo" accept="image/*" class="hidden">
                        </label>
                    </div>
                    <dl class="space-y-2">
                        @foreach (['Nama' => $user->name, 'Email' => $user->email, 'No. telepon' => data_get($user, 'phone') ?: '-', 'Bergabung' => $fmtDate($user->created_at)] as $label => $val)
                            <div class="flex justify-between gap-4 text-sm border-b border-sand/60 pb-2">
                                <dt class="text-espresso/50">{{ $label }}</dt><dd class="font-semibold text-right break-all">{{ $val }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <div>
                        <label class="text-xs font-bold tracking-wider uppercase text-coffee">Nama panggilan</label>
                        <input type="text" name="nickname" maxlength="50" value="{{ data_get($user, 'nickname') }}" placeholder="Mau dipanggil apa?"
                               class="mt-2 w-full rounded-xl border border-sand bg-white px-4 py-2.5 text-sm outline-none focus:border-gold focus:ring-0">
                    </div>
                    <button type="submit" class="w-full bg-espresso hover:bg-coffee text-cream text-sm font-semibold py-3 rounded-full transition">Simpan perubahan</button>
                </form>
            @else
                <p class="text-sm text-espresso/60">Masuk dulu untuk melihat profilmu.</p>
                <a href="{{ url('/login') }}" class="inline-block mt-4 bg-espresso text-cream text-xs font-bold px-6 py-2.5 rounded-full">Masuk</a>
            @endif
        </div>
    </div>

    <!-- Dock profil -->
    <div class="fixed bottom-5 left-5 z-40 flex flex-col items-center gap-3">
        <button type="button" id="profileBtn" aria-label="Profil saya" class="relative w-11 h-11 rounded-full ring-2 ring-gold shadow-lg overflow-hidden bg-gradient-to-br from-honey to-tan flex items-center justify-center hover:scale-105 transition" title="{{ $nick }}">
            <span class="absolute font-serif font-bold text-espresso text-lg">{!! $userInit !!}</span>
            @if ($userPhoto)<img src="{{ $userPhoto }}" alt="{{ $nick }}" class="relative w-full h-full object-cover" onerror="this.style.display='none'">@endif
        </button>
        <div class="relative">
            <button id="settingsBtn" type="button" aria-label="Pengaturan" class="w-11 h-11 rounded-full bg-espresso text-gold shadow-lg border border-gold/40 flex items-center justify-center hover:bg-mocha hover:rotate-45 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
            <div id="settingsMenu" class="absolute bottom-0 left-full ml-3 w-52 bg-white rounded-2xl border border-sand/60 shadow-[0_18px_40px_rgba(63,43,29,0.25)] p-2 text-sm">
                <div class="px-3 py-2 border-b border-sand/50 mb-1">
                    <p class="text-xs text-espresso/50">Masuk sebagai</p>
                    <p class="font-semibold truncate">{{ $nick }}</p>
                </div>
                <button type="button" data-go="history" class="w-full text-left px-3 py-2 rounded-lg hover:bg-cream transition">Riwayat peminjaman</button>
                <button type="button" id="profileBtn2" class="w-full text-left px-3 py-2 rounded-lg hover:bg-cream transition">Profil saya</button>
                @if ($user)
                    <form method="POST" action="{{ url('/logout') }}">@csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-700 hover:bg-red-50 transition">Keluar</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="block px-3 py-2 rounded-lg hover:bg-cream transition">Masuk</a>
                @endif
            </div>
        </div>
    </div>

    <script>
        const views = ['home','catalog','history'];
        const isLogged = @json((bool) $user);
        const csrf = @json(csrf_token());
        const me = @json(['name' => $user->name ?? '', 'phone' => data_get($user, 'phone') ?? '']);
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        let current = null;

        function countUp(root) {
            root.querySelectorAll('[data-count]').forEach(el => {
                const target = +el.dataset.count, dur = 900, start = performance.now();
                (function tick(now) {
                    const p = Math.min((now - start) / dur, 1);
                    el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(tick);
                })(start);
            });
        }

        function show(name, push = true) {
            if (!views.includes(name) || name === current) return;
            const next = document.getElementById(name);
            const prev = current ? document.getElementById(current) : null;
            const enter = () => {
                next.classList.add('active');
                requestAnimationFrame(() => requestAnimationFrame(() => next.classList.add('show')));
                if (name === 'home') countUp(next);
            };
            if (prev) { prev.classList.remove('show'); setTimeout(() => { prev.classList.remove('active'); enter(); }, 280); }
            else enter();
            current = name;
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.toggle('on', b.dataset.go === name));
            if (push) history.replaceState(null, '', name === 'home' ? location.pathname + location.search : '#' + name);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        const settingsBtn = document.getElementById('settingsBtn');
        const settingsMenu = document.getElementById('settingsMenu');
        document.querySelectorAll('[data-go]').forEach(b => b.addEventListener('click', () => { show(b.dataset.go); settingsMenu.classList.remove('open'); }));
        settingsBtn.addEventListener('click', e => { e.stopPropagation(); settingsMenu.classList.toggle('open'); });
        document.addEventListener('click', e => { if (!settingsMenu.contains(e.target)) settingsMenu.classList.remove('open'); });

        // Modal detail buku
        const modal = document.getElementById('bookModal');
        const $ = id => document.getElementById(id);
        function showBorrowForm(b, act) {
            const chips = [7].map((d, i) => '<label class="cursor-pointer"><input type="radio" name="duration" value="' + d + '" class="peer sr-only" ' + (i === 0 ? 'checked' : '') + '><span class="block text-center text-xs font-semibold px-4 py-2 rounded-xl border border-sand bg-white text-mocha peer-checked:bg-espresso peer-checked:text-cream peer-checked:border-espresso transition">' + d + ' hari</span></label>').join('');
            const f = 'w-full rounded-xl border border-sand bg-white px-4 py-2.5 text-sm outline-none focus:border-gold focus:ring-0';
            act.innerHTML = '<form method="POST" action="/books/' + b.id + '/borrow" class="space-y-3 bg-white/70 border border-sand rounded-2xl p-4">' +
                '<input type="hidden" name="_token" value="' + csrf + '">' +
                '<p class="text-xs font-bold tracking-wider uppercase text-coffee">Data peminjaman</p>' +
                '<input name="name" required value="' + esc(me.name) + '" placeholder="Nama lengkap" class="' + f + '">' +
                '<input name="phone" type="tel" required value="' + esc(me.phone) + '" placeholder="No. telepon / WhatsApp" class="' + f + '">' +
                '<div><p class="text-xs text-espresso/60 mb-2">Lama peminjaman</p><div class="flex gap-2">' + chips + '</div></div>' +
                '<p id="dueInfo" class="text-xs text-espresso/60"></p>' +
                '<div class="flex items-center gap-2 pt-1"><button type="submit" class="bg-espresso hover:bg-coffee text-cream text-sm font-semibold px-6 py-2.5 rounded-full transition">Ajukan peminjaman</button><button type="button" id="borrowCancel" class="text-sm font-semibold text-coffee px-4">Batal</button></div></form>';
            const upd = () => {
                const d = +act.querySelector('[name=duration]:checked').value, t = new Date();
                t.setDate(t.getDate() + d);
                document.getElementById('dueInfo').textContent = 'Kembalikan paling lambat ' + t.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            };
            act.querySelectorAll('[name=duration]').forEach(r => r.addEventListener('change', upd));
            upd();
            document.getElementById('borrowCancel').onclick = () => openBook(b);
        }
        function openBook(b) {
            $('mTitle').textContent = b.title;
            $('mAuthor').textContent = b.authors;
            $('mCat').textContent = b.category;
            $('mYear').textContent = b.year ? 'Terbit ' + b.year : '';
            $('mSyn').textContent = b.synopsis;
            $('mIsbn').textContent = b.isbn;
            $('mInit').textContent = (b.title || '?').charAt(0).toUpperCase();
            const img = $('mImg');
            img.classList.toggle('hidden', !b.image);
            if (b.image) { img.src = b.image; img.onerror = () => img.classList.add('hidden'); }

            const ok = b.stock > 0, st = $('mStock');
            st.textContent = ok ? 'Stok tersedia: ' + b.stock + ' buku' : 'Stok habis';
            st.className = 'text-xs font-bold px-3 py-1.5 rounded-full ' + (ok ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800');

            const act = $('mAction');
            if (!isLogged) {
                act.innerHTML = '<a href="{{ url('/login') }}" class="inline-block bg-espresso text-cream text-sm font-semibold px-7 py-3 rounded-full">Masuk untuk meminjam</a>';
            } else {
                act.innerHTML = '<button type="button" id="borrowOpen" ' + (ok ? '' : 'disabled') + ' class="bg-espresso hover:bg-coffee text-cream text-sm font-semibold px-7 py-3 rounded-full transition disabled:opacity-40 disabled:cursor-not-allowed">Pinjam buku</button>';
                if (ok) $('borrowOpen').onclick = () => showBorrowForm(b, act);
            }
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeBook() { modal.classList.remove('open'); document.body.style.overflow = ''; }
        document.addEventListener('click', e => {
            const c = e.target.closest('.book-card');
            if (c) openBook(JSON.parse(c.dataset.book));
        });
        $('modalClose').addEventListener('click', closeBook);
        modal.addEventListener('click', e => { if (e.target === modal) closeBook(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeBook(); });

        const pm = $('profileModal');
        ['profileBtn', 'profileBtn2'].forEach(id => $(id).addEventListener('click', () => { pm.classList.add('open'); settingsMenu.classList.remove('open'); }));
        const closeProfile = () => pm.classList.remove('open');
        $('profileClose').addEventListener('click', closeProfile);
        pm.addEventListener('click', e => { if (e.target === pm) closeProfile(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProfile(); });
        const photoInput = $('photoInput');
        if (photoInput) photoInput.addEventListener('change', e => {
            const f = e.target.files[0]; if (!f) return;
            const r = new FileReader();
            r.onload = x => { $('photoPrev').src = x.target.result; $('photoPrev').classList.remove('hidden'); };
            r.readAsDataURL(f);
        });

        const params = new URLSearchParams(location.search);
        const hasFilter = ['q','penulis','kategori','urut'].some(k => params.has(k));
        const initial = location.hash.slice(1) || (hasFilter ? 'catalog' : 'home');
        show(views.includes(initial) ? initial : 'home', false);
    </script>
</body>
</html>