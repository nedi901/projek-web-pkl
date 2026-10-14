<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Neraca SDM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#3a3a3a] min-h-screen flex items-center justify-center">
    <div class="w-[340px] bg-white rounded-lg p-8 shadow-xl">

        <div class="flex flex-col items-center mb-4">
            <img src="{{ asset('images/logo-esdm.png') }}" alt="Kementerian ESDM" class="h-32 w-auto mb-1">
        </div>

        <p class="text-center text-xs font-medium text-gray-800 leading-snug mb-6">
            Sistem Informasi Neraca<br>
            Sumber Daya Mineral, Batubara,<br>
            dan Panas Bumi Indonesia
        </p>

        @if (session('success'))
            <div class="mb-4 text-xs text-green-700 bg-green-50 border border-green-200 rounded-md px-3 py-2 text-center">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-xs text-red-600 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-xs text-gray-600 mb-1">Email atau Username</label>
                <input type="text" name="login" value="{{ old('login') }}" placeholder="email atau username" autocomplete="username"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-300" required autofocus>
            </div>
            <div class="mb-5">
                <label class="block text-xs text-gray-600 mb-1">Password</label>
                <input type="password" name="password" placeholder="Value"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm placeholder-gray-300" required>
            </div>
            <button type="submit" class="w-full bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold py-2.5 rounded-md">
                Masuk
            </button>
        </form>

        <div class="text-center text-xs text-gray-400 my-3">Atau</div>

        <a href="{{ route('user.dashboard') }}" class="block text-center bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold py-2.5 rounded-md">
            Lanjutkan Sebagai Tamu
        </a>
    </div>
</body>
</html>