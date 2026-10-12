<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Akun - Neraca SDM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#3a3a3a] min-h-screen flex items-center justify-center py-8">
    <div class="w-[440px] max-w-[94vw] bg-white rounded-lg p-8 shadow-xl">

        <div class="flex flex-col items-center mb-4">
            <svg width="48" height="48" viewBox="0 0 56 56" class="mb-2">
                <circle cx="28" cy="28" r="26" fill="none" stroke="#1a1a1a" stroke-width="2.5"/>
                <path d="M28 8 L44 20 L44 36 L28 48 L12 36 L12 20 Z" fill="none" stroke="#F2C230" stroke-width="2"/>
                <circle cx="28" cy="28" r="9" fill="#F2C230"/>
                <circle cx="28" cy="28" r="4" fill="#1a1a1a"/>
            </svg>
            <div class="text-lg font-extrabold tracking-wide text-[#1a1a1a]">Buat Akun</div>
            <p class="text-[11px] text-gray-500 text-center mt-1">
                Akun baru aktif setelah disetujui Super User.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-xs text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('daftar.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required minlength="3" maxlength="30" autocomplete="off"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="150"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Ulangi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
            </div>
            <label class="inline-flex items-center gap-2 text-[11px] text-gray-500">
                <input type="checkbox" onclick="['password','password_confirmation'].forEach(function(i){var e=document.getElementById(i);e.type=e.type==='password'?'text':'password';})">
                Tampilkan password
            </label>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Role yang Diminta</label>
                <select name="role_diminta" id="role_diminta" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    @foreach(\App\Models\User::ROLES as $val => $lbl)
                        <option value="{{ $val }}" @selected(old('role_diminta', 'admin') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1">Super User: kendali penuh. Admin: kelola 1 domain. User: hanya melihat data.</p>
            </div>

            <div id="domain-box">
                <label class="block text-xs text-gray-600 mb-1">Domain yang Dikelola</label>
                <select name="domain_diminta" id="domain_diminta" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">— pilih domain —</option>
                    @foreach(\App\Models\User::DOMAINS as $val => $lbl)
                        <option value="{{ $val }}" @selected(old('domain_diminta') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Instansi / Keperluan <span class="text-gray-400">(opsional)</span></label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}" maxlength="255"
                       placeholder="mis. PKL bidang Batubara"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <button type="submit" class="w-full bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold py-2.5 rounded-md mt-2">
                Kirim Pendaftaran
            </button>
        </form>

        <div class="text-center text-xs text-gray-500 mt-4">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-[#1a1a1a] underline">Masuk</a>
        </div>
    </div>

    <script>
        (function () {
            var role = document.getElementById('role_diminta');
            var box = document.getElementById('domain-box');
            var dom = document.getElementById('domain_diminta');
            function sinkron() {
                var admin = role.value === 'admin';
                box.style.display = admin ? '' : 'none';
                dom.required = admin;
                if (!admin) { dom.value = ''; }
            }
            role.addEventListener('change', sinkron);
            sinkron();
        })();
    </script>
</body>
</html>
