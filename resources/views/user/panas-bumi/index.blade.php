<x-layouts.user title="Data Panas Bumi">
    <h1 class="text-xl font-bold mb-1">Data Panas Bumi</h1>
    <p class="text-xs text-gray-500 mb-4">Sumber Daya & Cadangan per Lokasi</p>

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama lokasi..."
               class="border-gray-300 rounded-md text-sm flex-1 min-w-[200px]">

        <select name="provinsi_id" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua Provinsi</option>
            @foreach(\App\Models\Provinsi::orderBy('nama_provinsi')->get() as $p)
                <option value="{{ $p->id }}" @selected(request('provinsi_id')==$p->id)>{{ $p->nama_provinsi }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Filter</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama Lokasi</th>
                        <th class="px-3 py-2 text-left">Provinsi</th>
                        <th class="px-3 py-2 text-right">Spekulatif</th>
                        <th class="px-3 py-2 text-right">Hipotetis</th>
                        <th class="px-3 py-2 text-right">Terduga</th>
                        <th class="px-3 py-2 text-right">Cad. Terbukti</th>
                        <th class="px-3 py-2 text-right">Kapasitas (MWe)</th>
                        <th class="px-3 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50 cursor-pointer"
                        onclick="window.location='{{ route('user.panas-bumi.show', $row) }}'">
                        <td class="px-3 py-2 font-medium">{{ $row->nama_objek }}</td>
                        <td class="px-3 py-2">{{ $row->provinsi->nama_provinsi }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->spekulatif, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->hipotetis, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->terduga, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->terbukti, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->kapasitas_terpasang, 2) }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded-full text-xs {{ $row->statDikBb->label == 'Operasi Produksi' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $row->statDikBb->label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-6 text-gray-400">Data belum ada</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</x-layouts.user>