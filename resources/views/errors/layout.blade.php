<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('kode') - @yield('judul') | Neraca SDM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#3a3a3a] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-lg p-8 shadow-xl text-center">
        <img src="{{ asset('images/logo-esdm.png') }}" alt="Kementerian ESDM" class="h-24 w-auto mx-auto mb-2">

        <p class="text-6xl font-bold text-gray-800 leading-none mb-2">@yield('kode')</p>
        <h1 class="text-lg font-semibold text-gray-800 mb-2">@yield('judul')</h1>
        <p class="text-sm text-gray-600 mb-6">@yield('pesan')</p>

        @yield('tambahan')

        <div class="flex gap-2 justify-center">
            <a href="javascript:history.back()"
               class="px-4 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Kembali</a>
            <a href="{{ url('/') }}"
               class="px-4 py-2 text-sm rounded-md bg-gray-800 text-white hover:bg-gray-700">Ke Beranda</a>
        </div>
    </div>
</body>
</html>
