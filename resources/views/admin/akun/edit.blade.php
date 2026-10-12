<x-layouts.admin title="Edit Akun">
    <div class="max-w-2xl">
        <h1 class="text-xl font-bold mb-1">Edit Akun</h1>
        <p class="text-xs text-gray-500 mb-4">
            Dibuat {{ $akun->created_at?->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') }} WIB ·
            Login terakhir:
            {{ $akun->last_login_at ? $akun->last_login_at->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : 'belum pernah' }}
        </p>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('superuser.akun.update', $akun) }}" class="bg-white border rounded-xl p-6 mb-6">
            @csrf
            @method('PUT')
            @include('admin.akun._form', ['akun' => $akun])

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-5 py-2 rounded-md">Simpan Perubahan</button>
                <a href="{{ route('superuser.akun.index') }}" class="text-sm text-gray-500 px-3 py-2">Kembali</a>
            </div>
        </form>

        <form method="POST" action="{{ route('superuser.akun.password', $akun) }}" class="bg-white border rounded-xl p-6">
            @csrf
            @method('PUT')
            <h2 class="text-sm font-semibold mb-1">Reset Password</h2>
            <p class="text-xs text-gray-500 mb-4">Atur password baru untuk akun ini (misalnya kalau lupa). Sampaikan password barunya langsung ke pemilik akun.</p>

            @if($errors->reset->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2 mb-3">
                    @foreach($errors->reset->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
            <button type="submit" class="mt-4 bg-gray-800 text-white text-sm px-5 py-2 rounded-md">Reset Password</button>
        </form>
    </div>
</x-layouts.admin>
