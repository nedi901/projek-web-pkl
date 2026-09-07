<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Neraca SDM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#3a3a3a] min-h-screen flex items-center justify-center">
    <div class="w-[340px] bg-white rounded-lg p-8 shadow-xl">

        <div class="flex flex-col items-center mb-4">
            <svg width="56" height="56" viewBox="0 0 56 56" class="mb-2">
                <circle cx="28" cy="28" r="26" fill="none" stroke="#1a1a1a" stroke-width="2.5"/>
                <path d="M28 8 L44 20 L44 36 L28 48 L12 36 L12 20 Z" fill="none" stroke="#F2C230" stroke-width="2"/>
                <circle cx="28" cy="28" r="9" fill="#F2C230"/>
                <circle cx="28" cy="28" r="4" fill="#1a1a1a"/>
            </svg>
            <div class="text-[10px] font-semibold tracking-wide text-gray-700">KEMENTERIAN</div>
            <div class="text-lg font-extrabold tracking-wide text-[#1a1a1a] -mt-1">ESDM</div>
        </div>

        <p class="text-center text-xs font-medium text-gray-800 leading-snug mb-6">
            Sistem Informasi Neraca<br>
            Sumber Daya Mineral, Batubara,<br>
            dan Panas Bumi Indonesia
        </p>

        @if ($errors->any())
            <div class="mb-4 text-xs text-red-600 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-xs text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Value"
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