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

        <form method="POST" action="{{ route('superuser.akun.update', $akun) }}" class="bg-white border rounded-xl p-6">
            @csrf
            @method('PUT')
            @include('admin.akun._form', ['akun' => $akun])

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-5 py-2 rounded-md">Simpan Perubahan</button>
                <a href="{{ route('superuser.akun.index') }}" class="text-sm text-gray-500 px-3 py-2">Kembali</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
