<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin - Neraca SDM' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F5F5F5] text-gray-800">
    <div class="flex min-h-screen">
        <div class="w-56 bg-white border-r border-gray-200 flex flex-col shrink-0">
            <div class="flex flex-col items-center py-6 border-b border-gray-100">
                <img src="{{ asset('images/logo-esdm.png') }}" alt="Kementerian ESDM" class="h-24 w-auto">
            </div>

            <nav class="flex-1 py-3 px-3 space-y-1 text-sm overflow-y-auto">
    @php $domain = auth()->user()->domain_akses; @endphp

    @if(auth()->user()->role === 'super_user')
        <a href="{{ route('superuser.dashboard') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('superuser.dashboard') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Dashboard
        </a>
    @endif

    @if($domain === 'batubara' || auth()->user()->role === 'super_user')
        @if(auth()->user()->role === 'super_user')<div class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-wide text-gray-400">Batubara</div>@endif
        <a href="{{ route('admin.batubara.grafik.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.batubara.grafik.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Grafik dan Visualisasi
        </a>
        <a href="{{ route('admin.batubara.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.batubara.*') && !request()->routeIs('admin.batubara.grafik.*') && !request()->routeIs('admin.batubara.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Data Batubara
        </a>
        <a href="{{ route('admin.batubara.histori.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.batubara.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Histori
        </a>
    @endif

    @if($domain === 'mineral_logam' || auth()->user()->role === 'super_user')
        @if(auth()->user()->role === 'super_user')<div class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-wide text-gray-400">Mineral Logam</div>@endif
        <a href="{{ route('admin.mineral-logam.grafik.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.mineral-logam.grafik.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Grafik dan Visualisasi
        </a>
        <a href="{{ route('admin.mineral-logam.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.mineral-logam.*') && !request()->routeIs('admin.mineral-logam.grafik.*') && !request()->routeIs('admin.mineral-logam.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Data Mineral Logam
        </a>
        <a href="{{ route('admin.mineral-logam.histori.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.mineral-logam.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Histori
        </a>
    @endif

    @if($domain === 'mineral_bukan_logam' || auth()->user()->role === 'super_user')
        @if(auth()->user()->role === 'super_user')<div class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-wide text-gray-400">Mineral Bukan Logam</div>@endif
        <a href="{{ route('admin.mineral-bukan-logam.grafik.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.mineral-bukan-logam.grafik.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Grafik dan Visualisasi
        </a>
        <a href="{{ route('admin.mineral-bukan-logam.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.mineral-bukan-logam.*') && !request()->routeIs('admin.mineral-bukan-logam.grafik.*') && !request()->routeIs('admin.mineral-bukan-logam.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Data Mineral Bukan Logam
        </a>
        <a href="{{ route('admin.mineral-bukan-logam.histori.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.mineral-bukan-logam.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Histori
        </a>
    @endif

    @if($domain === 'panas_bumi' || auth()->user()->role === 'super_user')
    @if(auth()->user()->role === 'super_user')<div class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-wide text-gray-400">Panas Bumi</div>@endif
        <a href="{{ route('admin.panas-bumi.grafik.index') }}"
       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.panas-bumi.grafik.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
        Grafik dan Visualisasi
    </a>
    <a href="{{ route('admin.panas-bumi.index') }}"
       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.panas-bumi.*') && !request()->routeIs('admin.panas-bumi.grafik.*') && !request()->routeIs('admin.panas-bumi.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
        Data Panas Bumi
    </a>
        <a href="{{ route('admin.panas-bumi.histori.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.panas-bumi.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Histori
        </a>
@endif

@if($domain === 'gambut' || auth()->user()->role === 'super_user')
    @if(auth()->user()->role === 'super_user')<div class="px-3 pt-3 pb-1 text-[10px] uppercase tracking-wide text-gray-400">Gambut</div>@endif
        <a href="{{ route('admin.gambut.grafik.index') }}"
       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.gambut.grafik.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
        Grafik dan Visualisasi
    </a>
    <a href="{{ route('admin.gambut.index') }}"
       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.gambut.*') && !request()->routeIs('admin.gambut.grafik.*') && !request()->routeIs('admin.gambut.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
        Data Gambut
    </a>
        <a href="{{ route('admin.gambut.histori.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('admin.gambut.histori.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Histori
        </a>
@endif


@if(auth()->user()->role === 'super_user')
        <div class="px-3 pt-4 pb-1 text-[10px] uppercase tracking-wide text-gray-400">Pengaturan</div>
        <a href="{{ route('superuser.akun.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('superuser.akun.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Kelola Akun
        </a>
        <a href="{{ route('superuser.referensi.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('superuser.referensi.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Kelola Referensi
        </a>
        <a href="{{ route('superuser.tabel-buku.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('superuser.tabel-buku.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Kelola Tabel Buku
        </a>
        <a href="{{ route('superuser.histori-semua.index') }}"
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg font-medium {{ request()->routeIs('superuser.histori-semua.*') ? 'bg-[#F2C230] text-black' : 'text-gray-600 hover:bg-gray-50' }}">
            Histori Semua Domain
        </a>
@endif

</nav>

            <div class="border-t border-gray-100 px-3 py-3">
                <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs">
                    <div class="font-medium text-gray-700">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-gray-400 font-mono">{{ auth()->user()->role === 'super_user' ? 'SUPER USER' : 'ADMIN · ' . strtoupper(str_replace('_', ' ', auth()->user()->domain_akses ?? '-')) }}</div>
                </div>
                <a href="{{ route('akun-saya.edit') }}" class="block mt-2 px-3 py-2 text-sm rounded-lg {{ request()->routeIs('akun-saya.*') ? 'bg-[#F2C230] text-black font-medium' : 'text-gray-500 hover:bg-gray-50' }}">Akun Saya</a>
                <form method="POST" action="{{ route('logout') }}" class="">
                    @csrf
                    <button class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 rounded-lg">Logout</button>
                </form>
            </div>
        </div>

        <div class="flex-1 min-w-0 p-8">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-2.5 mb-4">
                {{ session('success') }}
            </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</body>
</html>
