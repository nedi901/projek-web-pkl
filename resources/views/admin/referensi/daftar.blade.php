<x-layouts.admin :title="$cfg['label']">
    @php $slug = $cfg['slug']; @endphp

    <div class="flex justify-between items-end mb-4">
        <div>
            <a href="{{ route('superuser.referensi.index') }}" class="text-xs text-gray-400 hover:underline">← Kelola Referensi</a>
            <h1 class="text-xl font-bold">{{ $cfg['label'] }}</h1>
        </div>
        <a href="{{ route('superuser.referensi.create', $slug) }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">+ Tambah</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('error') }}</div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="border-gray-300 rounded-md text-sm flex-1 min-w-[200px]">
        @if($slug === 'kabupaten')
            <select name="provinsi_id" class="border-gray-300 rounded-md text-sm">
                <option value="">Semua provinsi</option>
                @foreach($provinsis as $p)
                    <option value="{{ $p->id }}" @selected(request('provinsi_id') == $p->id)>{{ $p->nama_provinsi }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Cari</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left w-14">No</th>
                        @foreach($cfg['kolom'] as $k)
                            <th class="px-3 py-2 text-left">{{ $k['l'] }}</th>
                        @endforeach
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $i => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-400">{{ $rows->firstItem() + $i }}</td>
                            @foreach($cfg['kolom'] as $k)
                                <td class="px-3 py-2">
                                    @if($k['tipe'] === 'relasi')
                                        {{ $relasiOpsi[$k['k']]->firstWhere('id', $row->{$k['k']})?->{$k['tampil']} ?? '-' }}
                                    @else
                                        {{ $row->{$k['k']} ?: '-' }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                <a href="{{ route('superuser.referensi.edit', [$slug, $row->id]) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                                <form method="POST" action="{{ route('superuser.referensi.destroy', [$slug, $row->id]) }}" class="inline"
                                      onsubmit="return confirm('Hapus data ini? Jika masih dipakai data lain, penghapusan akan ditolak.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($cfg['kolom']) + 2 }}" class="text-center py-6 text-gray-400">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</x-layouts.admin>
