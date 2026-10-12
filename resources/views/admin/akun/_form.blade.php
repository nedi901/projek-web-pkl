{{-- Form akun oleh Super User. Variabel: $akun (null saat tambah baru) --}}
@php
    $edit = isset($akun) && $akun;
    $saya = $edit && $akun->id === auth()->id();
    $roleSekarang = old('role', $edit ? $akun->role : 'admin');
@endphp

@if($errors->default->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5 mb-4">
        <div class="font-medium mb-1">Data belum valid:</div>
        <ul class="list-disc list-inside text-xs">
            @foreach($errors->default->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs text-gray-600 mb-1">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $edit ? $akun->name : '') }}" required maxlength="100"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-600 mb-1">Username</label>
        <input type="text" name="username" value="{{ old('username', $edit ? $akun->username : '') }}" required minlength="3" maxlength="30"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono" autocomplete="off">
        <p class="text-[10px] text-gray-400 mt-1">Huruf kecil, angka, - dan _ (3–30 karakter). Dipakai untuk login.</p>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-xs text-gray-600 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $edit ? $akun->email : '') }}" required maxlength="150"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-xs text-gray-600 mb-1">Role</label>
        <select name="role" id="role" @disabled($saya) class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            @foreach(\App\Models\User::ROLES as $val => $lbl)
                <option value="{{ $val }}" @selected($roleSekarang === $val)>{{ $lbl }}</option>
            @endforeach
        </select>
        @if($saya)
            <input type="hidden" name="role" value="{{ $akun->role }}">
        @else
            <p class="text-[10px] text-gray-400 mt-1">Super User: kendali penuh. Admin: kelola 1 domain. User: hanya melihat data.</p>
        @endif
    </div>

    <div id="domain-box">
        <label class="block text-xs text-gray-600 mb-1">Domain yang Dikelola (khusus Admin)</label>
        <select name="domain_akses" id="domain_akses" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="">— pilih domain —</option>
            @foreach(\App\Models\User::DOMAINS as $val => $lbl)
                <option value="{{ $val }}" @selected(old('domain_akses', $edit ? $akun->domain_akses : '') === $val)>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>


    @unless($edit)
        <div>
            <label class="block text-xs text-gray-600 mb-1">Password</label>
            <div class="flex gap-2">
                <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <button type="button" onclick="togglePw()" class="text-xs border border-gray-300 rounded-md px-3">Lihat</button>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">Minimal 8 karakter.</p>
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Ulangi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>
    @endunless

    <div class="sm:col-span-2">
        @if($saya)
            <input type="hidden" name="is_active" value="1">
        @else
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $edit ? $akun->is_active : true)) class="rounded border-gray-300">
                Akun aktif (bisa login)
            </label>
        @endif
    </div>
</div>

<script>
    (function () {
        var role = document.getElementById('role');
        var box = document.getElementById('domain-box');
        var dom = document.getElementById('domain_akses');
        function sinkron() {
            var admin = role.value === 'admin';
            box.style.display = admin ? '' : 'none';
            dom.required = admin;
            if (!admin) { dom.value = ''; }
        }
        role.addEventListener('change', sinkron);
        sinkron();
    })();
    function togglePw() {
        ['password', 'password_confirmation'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.type = el.type === 'password' ? 'text' : 'password'; }
        });
    }
</script>
