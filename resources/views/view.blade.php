<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Cendekia</title>
    
    <!-- Script Tailwind CDN (Pastikan temanmu menyesuaikan ini dengan Vite jika mereka sudah setup Node.js) -->
    <script src="https://cdn.tailwindcss.com"></script>
     @vite('resources/css/app.css')
</head>
<body class="bg-linear-to-br from-[#f6f3f0] to-[#eaddd7] min-h-screen flex items-center justify-center p-4 md:p-10 font-sans">

    <!-- Kontainer utama (Kartu Putih Raksasa) -->
    <div class="max-w-[1200px] w-full mx-auto bg-white rounded-[40px] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.08)] p-10 md:p-14 relative overflow-hidden">

        <!-- Navigasi -->
        <nav class="flex justify-between items-center mb-20 relative z-20">
            <div class="text-2xl font-extrabold text-gray-900 tracking-tight">Cendekia</div>
            
            <div class="hidden md:flex space-x-10 text-xs font-semibold text-gray-400">
                <a href="#" class="hover:text-gray-900 transition">Book Type</a>
                <a href="#" class="hover:text-gray-900 transition">Recomendation</a>
                <a href="#" class="hover:text-gray-900 transition">Popular</a>
                <a href="#" class="hover:text-gray-900 transition">Download App</a>
            </div>
            
            <div class="flex items-center space-x-6">
                <a href="#" class="hidden md:block text-xs font-semibold text-gray-500 hover:text-gray-900">Login</a>
                <button class="bg-[#111111] text-white px-7 py-3 rounded-full text-xs font-semibold hover:bg-black transition shadow-lg">
                    Start For Free
                </button>
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
                    <button class="bg-[#9cd49b] hover:bg-[#8bc38d] text-white px-8 py-3.5 rounded-full text-sm font-semibold transition shadow-md">
                        Search
                    </button>
                </div>
            </div>

            <!-- Kanan: Area Gambar 3D & Ikon Melayang -->
            <div class="md:w-[45%] relative mt-16 md:mt-0 flex justify-center items-center h-[400px]">
                
                <!-- Gambar Tumpukan Buku Langsung dari Internet -->
                <div class="relative z-10 w-[110%] flex items-center justify-center transform hover:scale-105 transition duration-500">
                    <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=800&q=80" alt="Tumpukan Buku" class="object-cover rounded-[40px] shadow-[0_20px_50px_rgba(0,0,0,0.15)] h-[300px] w-full">
                </div>

                <!-- Ikon Dekorasi 3D Melayang -->
                <div class="absolute top-[-10px] left-[0%] w-16 h-16 bg-linear-to-br from-[#c0ecbd] to-[#9ad698] rounded-[1.2rem] flex items-center justify-center shadow-[0_15px_30px_rgba(156,212,155,0.4)] z-30 transform -rotate-6">
                    <span class="text-white text-2xl drop-shadow-md">💡</span>
                </div>
                
                <div class="absolute top-[20%] right-[-5%] w-16 h-16 bg-linear-to-br from-[#c8c8f0] to-[#a2a2d6] rounded-[1.2rem] flex items-center justify-center shadow-[0_15px_30px_rgba(162,162,214,0.4)] z-30 transform rotate-12">
                    <span class="text-white text-2xl drop-shadow-md">⭐</span>
                </div>
                
                <div class="absolute bottom-[-10px] left-[15%] w-16 h-16 bg-linear-to-br from-[#ffdfbc] to-[#ffb875] rounded-[1.2rem] flex items-center justify-center shadow-[0_15px_30px_rgba(255,184,117,0.4)] z-30 transform rotate-3">
                    <span class="text-white text-2xl drop-shadow-md">🎯</span>
                </div>
            </div>
        </div>

        <!-- Bagian Footer (3 Kolom) -->
        <div class="mt-32 pt-8 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-10 relative z-20">
            <div class="pr-6">
                <span class="text-[#9cd49b] font-bold text-xs tracking-wider mb-3 block">New Arrived</span>
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

    </div>
</body>
</html>