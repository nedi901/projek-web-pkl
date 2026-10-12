<x-layouts.admin title="Kelola Tabel Buku">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold">Kelola Tabel Buku</h1>
            <p class="text-xs text-gray-500">Tabel rekapitulasi yang tampil di bagian "Tabel Data" pada halaman Grafik dan Visualisasi tiap domain.</p>
        </div>
        <a href="{{ route('superuser.tabel-buku.create') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">+ Tambah Tabel</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tabel..." class="border-gray-300 rounded-md text-sm flex-1 min-w-[200px]">
        <select name="domain" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua domain</option>
            @foreach($domains as $slug => [$label])
                <option value="{{ $slug }}" @selected($domain === $slug)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Filter</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Domain</th>
                        <th class="px-3 py-2 text-left">Judul</th>
                        <th class="px-3 py-2 text-left">Tahun</th>
                        <th class="px-3 py-2 text-left">Ukuran</th>
                        <th class="px-3 py-2 text-left">Urutan</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tabels as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $domains[$t->domain][0] ?? $t->domain }}</td>
                            <td class="px-3 py-2">
                                <div class="font-medium">{{ $t->judul }}</div>
                                <div class="text-[10px] text-gray-400">{{ $t->sumber }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $t->tahun_data ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">{{ count($t->baris) }} baris × {{ count($t->kolom) }} kolom</td>
                            <td class="px-3 py-2">{{ $t->urutan }}</td>
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                @if(isset($domains[$t->domain]))
                                    <a href="{{ route($domains[$t->domain][1] . '.grafik.index') }}" class="text-gray-500 hover:underline mr-3">Lihat</a>
                                @endif
                                <a href="{{ route('superuser.tabel-buku.edit', $t) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                                <form method="POST" action="{{ route('superuser.tabel-buku.destroy', $t) }}" class="inline"
                                      onsubmit="return confirm('Hapus tabel ini dari halaman Grafik?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-6 text-gray-400">Belum ada tabel</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $tabels->links() }}</div>
</x-layouts.admin>
