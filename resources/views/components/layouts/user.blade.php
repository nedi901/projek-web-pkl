<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Neraca SDM' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F5F5F5] text-gray-800">
    <div class="flex min-h-screen">
        <div class="w-56 bg-white border-r border-gray-200 flex flex-col shrink-0">
            <div class="flex flex-col items-center py-6 border-b border-gray-100">
                <img src="{{ asset('images/logo-esdm.png') }}" alt="Kementerian ESDM" class="h-20 w-auto">
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
                    {{ auth()->user()->name }}
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
        <div class="flex-1 min-w-0 p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>