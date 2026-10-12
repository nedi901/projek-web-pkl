<x-layouts.user title="Data Gambut">
    <h1 class="text-xl font-bold mb-1">Data Gambut</h1>
    <p class="text-xs text-gray-500 mb-4">Sumber Daya Gambut per Provinsi (Gambut belum memiliki data cadangan)</p>

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama data..."
               class="border-gray-300 rounded-md text-sm flex-1 min-w-[200px]">

        <select name="provinsi_id" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua Provinsi</option>
            @foreach(\App\Models\Provinsi::orderBy('nama_provinsi')->get() as $p)
                <option value="{{ $p->id }}" @selected(request('provinsi_id') == $p->id)>{{ $p->nama_provinsi }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Filter</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama Data</th>
                        <th class="px-3 py-2 text-left">Provinsi</th>
                        <th class="px-3 py-2 text-left">Nilai Kalori</th>
                        <th class="px-3 py-2 text-right">Luas (ha)</th>
                        <th class="px-3 py-2 text-right">Volume (juta m³)</th>
                        <th class="px-3 py-2 text-right">Sumber Daya (juta ton)</th>
                        <th class="px-3 py-2 text-left">Tahun</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50 cursor-pointer"
                        onclick="window.location='{{ route('user.gambut.show', $row) }}'">
                        <td class="px-3 py-2 font-medium">{{ $row->nama_objek }}</td>
                        <td class="px-3 py-2">{{ $row->provinsi->nama_provinsi ?? '-' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->nilai_kalori_label }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->luas_ha, 2, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->volume_juta_m3, 2, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->total_sd, 2, ',', '.') }}</td>
                        <td class="px-3 py-2">{{ $row->tahun_data }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-6 text-gray-400">Data belum ada</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</x-layouts.user>
