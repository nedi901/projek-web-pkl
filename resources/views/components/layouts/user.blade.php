<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Neraca SDM' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.gaya-tampilan')
</head>
<body class="bg-[#F5F5F5] text-gray-800">
    {{-- Bilah atas + tombol menu: hanya tampil di layar kecil (HP/tablet kecil) --}}
    <header class="md:hidden sticky top-0 z-30 flex items-center gap-3 bg-white border-b border-gray-200 px-4 py-2.5">
        <button type="button" id="sidebar-buka" aria-label="Buka menu" aria-controls="sidebar"
                class="p-2 -ml-2 rounded-md text-gray-700 hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <img src="{{ asset('images/favicon.png') }}" alt="" class="h-7 w-7">
        <span class="text-sm font-semibold text-gray-800">Neraca SDM</span>
    </header>
    <div id="sidebar-overlay" class="hidden md:hidden fixed inset-0 z-30 bg-black/40"></div>

    <div class="flex min-h-screen">
        <div id="sidebar" class="fixed inset-y-0 left-0 z-40 -translate-x-full transition-transform duration-200 overflow-y-auto md:static md:z-auto md:translate-x-0 md:overflow-visible w-56 bg-white border-r border-gray-200 flex flex-col shrink-0">
            <div class="flex flex-col items-center py-6 border-b border-gray-100">
                <img src="{{ asset('images/logo-esdm.png') }}" alt="Kementerian ESDM" class="h-24 w-auto">
            </div>
            <nav class="flex-1 py-3 px-3 space-y-1 text-sm">
                <a href="{{ route('user.dashboard') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('user.dashboard') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
                    Beranda
                </a>
                <a href="{{ route('user.batubara.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('user.batubara.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
                    Data Batubara
                </a>
                <a href="{{ route('user.mineral-logam.index') }}"
   class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('user.mineral-logam.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
    Mineral Logam
</a>
<a href="{{ route('user.mineral-bukan-logam.index') }}"
   class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('user.mineral-bukan-logam.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
    Bukan Logam & Batuan
</a>
<a href="{{ route('user.panas-bumi.index') }}"
   class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('user.panas-bumi.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
    Data Panas Bumi
</a>
<a href="{{ route('user.gambut.index') }}"
   class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('user.gambut.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
    Data Gambut
</a>
<a href="{{ route('user.grafik.index') }}"
   class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('user.grafik.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
    Grafik dan Visualisasi
</a>
            </nav>
            <div class="border-t border-gray-100 p-3">
                @auth
                <div class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-600">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-6 h-6 rounded-full object-cover shrink-0">
                    @endif
                    <span class="truncate">{{ auth()->user()->name }}</span>
                </div>
                <a href="{{ route('akun-saya.edit') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('akun-saya.*') ? 'bg-[#F2C230] text-black font-medium' : 'text-gray-500 hover:bg-gray-50' }}">Akun Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 rounded-lg">Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                    Profil / Login
                </a>
                @endauth
            </div>
        </div>
        <div class="flex-1 min-w-0 p-4 sm:p-6 md:p-8">
            {{ $slot }}
        </div>
    </div>
    <script>
        // Menu samping di layar kecil: buka lewat tombol, tutup lewat latar gelap, tombol Esc, atau memilih menu
        (function () {
            var sb = document.getElementById('sidebar');
            var ov = document.getElementById('sidebar-overlay');
            var buka = document.getElementById('sidebar-buka');
            if (!sb || !ov || !buka) return;
            function bukaMenu() { sb.classList.remove('-translate-x-full'); ov.classList.remove('hidden'); }
            function tutupMenu() { sb.classList.add('-translate-x-full'); ov.classList.add('hidden'); }
            buka.addEventListener('click', bukaMenu);
            ov.addEventListener('click', tutupMenu);
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutupMenu(); });
            sb.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', tutupMenu); });
        })();
    </script>
</body>
</html>