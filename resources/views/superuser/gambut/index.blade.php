<x-layouts.superuser title="Data Gambut">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold">Data Gambut</h1>
            <p class="text-xs text-gray-500">Kelola data neraca — Super User</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superuser.gambut.import.form') }}" class="border border-gray-300 text-sm font-semibold px-4 py-2 rounded-md">
                Import Excel
            </a>
            <a href="{{ route('superuser.gambut.create') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">
                + Tambah Data
            </a>
        </div>
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama lokasi..."
               class="border-gray-300 rounded-md text-sm flex-1 min-w-[200px]">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Cari</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama Lokasi</th>
                        <th class="px-3 py-2 text-left">Provinsi</th>
                        <th class="px-3 py-2 text-left">Nilai Kalori</th>
                        <th class="px-3 py-2 text-right">Luas (ha)</th>
                        <th class="px-3 py-2 text-right">Volume (juta m³)</th>
                        <th class="px-3 py-2 text-right">Sumber Daya (juta ton)</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $row->nama_objek }}</td>
                        <td class="px-3 py-2">{{ $row->provinsi->nama_provinsi }}</td>
                        <td class="px-3 py-2">{{ $row->nilai_kalori_label }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->luas_ha, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->volume_juta_m3, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->total_sd, 2) }}</td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('superuser.gambut.edit', $row) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                            <form method="POST" action="{{ route('superuser.gambut.destroy', $row) }}" class="inline"
                                  onsubmit="return confirm('Hapus data {{ $row->nama_objek }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-6 text-gray-400">Data belum ada</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</x-layouts.superuser>
