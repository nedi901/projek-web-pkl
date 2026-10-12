<x-layouts.admin title="Tambah Akun">
    <div class="max-w-2xl">
        <h1 class="text-xl font-bold mb-1">Tambah Akun</h1>
        <p class="text-xs text-gray-500 mb-4">Akun baru bisa login dengan email atau username.</p>

        <form method="POST" action="{{ route('superuser.akun.store') }}" class="bg-white border rounded-xl p-6">
            @csrf
            @include('admin.akun._form', ['akun' => null])

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-5 py-2 rounded-md">Simpan</button>
                <a href="{{ route('superuser.akun.index') }}" class="text-sm text-gray-500 px-3 py-2">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
