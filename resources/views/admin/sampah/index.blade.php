<x-layouts.admin :title="'Sampah ' . $label">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold">Sampah — {{ $label }}</h1>
            <p class="text-xs text-gray-500">Data yang dihapus disimpan di sini dan masih bisa dipulihkan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route($routePrefix . '.sampah.riwayat') }}" class="border border-gray-300 text-sm font-semibold px-4 py-2 rounded-md">
                Riwayat Aktivitas
            </a>
            <a href="{{ route($routePrefix . '.index') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">
                ← Kembali ke Data
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('error') }}</div>
    @endif

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama data di sampah..."
               class="border-gray-300 rounded-md text-sm flex-1 min-w-[200px]">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Cari</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama Data</th>
                        <th class="px-3 py-2 text-left">Provinsi</th>
                        <th class="px-3 py-2 text-left">Tahun Data</th>
                        <th class="px-3 py-2 text-left">Dihapus Oleh</th>
                        <th class="px-3 py-2 text-left">Dihapus Pada</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $row->nama_objek }}</td>
                        <td class="px-3 py-2">{{ $row->provinsi->nama_provinsi ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $row->tahun_data }}</td>
                        <td class="px-3 py-2">{{ $row->deletedBy->name ?? '(akun sudah tidak ada / tidak tercatat)' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->deleted_at?->format('d M Y, H:i') }}</td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <form method="POST" action="{{ route($routePrefix . '.sampah.restore', $row->id) }}" class="inline"
                                  onsubmit="return confirm('Pulihkan data ini?')">
                                @csrf
                                <button type="submit" class="text-green-700 hover:underline mr-3">Pulihkan</button>
                            </form>
                            @if(auth()->user()->role === 'super_user')
                            <form method="POST" action="{{ route($routePrefix . '.sampah.force', $row->id) }}" class="inline"
                                  onsubmit="return confirm('HAPUS PERMANEN data ini? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus Permanen</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-6 text-gray-400">Sampah kosong</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</x-layouts.admin>
