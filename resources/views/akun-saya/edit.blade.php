<x-dynamic-component :component="$akun->role === 'user' ? 'layouts.user' : 'layouts.admin'" title="Akun Saya">
    <div class="max-w-2xl">
        <h1 class="text-xl font-bold mb-1">Akun Saya</h1>
        <p class="text-xs text-gray-500 mb-4">
            {{ $akun->roleLabel() }}@if($akun->role === 'admin') · Domain {{ $akun->domainLabel() }}@endif
        </p>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('akun-saya.update') }}" class="bg-white border rounded-xl p-6 mb-6">
            @csrf
            @method('PUT')
            <h2 class="text-sm font-semibold mb-4">Profil</h2>

            @if($errors->default->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2 mb-3">
                    @foreach($errors->default->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $akun->name) }}" required maxlength="100"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $akun->username) }}" required minlength="3" maxlength="30"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-600 mb-1">Email</label>
                    <input type="email" value="{{ $akun->email }}" disabled
                           class="w-full border border-gray-200 bg-gray-50 text-gray-500 rounded-md px-3 py-2 text-sm cursor-not-allowed">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-5 py-2 rounded-md">Simpan Profil</button>
        </form>

        <form method="POST" action="{{ route('akun-saya.password') }}" class="bg-white border rounded-xl p-6">
            @csrf
            @method('PUT')
            <h2 class="text-sm font-semibold mb-4">Ganti Password</h2>

            @if($errors->password->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2 mb-3">
                    @foreach($errors->password->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-600 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Password Baru</label>
                    <input type="password" name="password" required minlength="8" autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Ulangi Password Baru</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-gray-800 text-white text-sm px-5 py-2 rounded-md">Ganti Password</button>
        </form>
    </div>
</x-dynamic-component>
