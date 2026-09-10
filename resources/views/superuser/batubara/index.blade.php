<x-layouts.superuser title="Data Batubara">
    
    <div class="flex justify-between items-end mb-4">
    <div>
        <h1 class="text-xl font-bold">Data Batubara</h1>
        <p class="text-xs text-gray-500">Kelola data neraca — Super User</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superuser.batubara.import.form') }}" class="border border-gray-300 text-sm font-semibold px-4 py-2 rounded-md">
            Import Excel
        </a>
        <a href="{{ route('superuser.batubara.create') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">
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
                        <th class="px-3 py-2 text-right">Tereka</th>
                        <th class="px-3 py-2 text-right">Terukur</th>
                        <th class="px-3 py-2 text-right">Total Cadangan</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $row->nama_objek }}</td>
                        <td class="px-3 py-2">{{ $row->provinsi->nama_provinsi }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->tereka, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->terukur, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->total_cad, 2) }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded-full text-xs {{ $row->statDikBb->label == 'Operasi Produksi' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $row->statDikBb->label }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('superuser.batubara.edit', $row) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                            <form method="POST" action="{{ route('superuser.batubara.destroy', $row) }}" class="inline"
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