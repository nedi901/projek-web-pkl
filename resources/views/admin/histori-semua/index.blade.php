<x-layouts.admin title="Histori Semua Domain">
    @php
        $badge = ['dihapus' => 'bg-yellow-100 text-yellow-800', 'dipulihkan' => 'bg-green-100 text-green-800', 'dihapus_permanen' => 'bg-red-100 text-red-800'];
        $tabAktif = 'bg-[#F2C230] text-black font-semibold';
        $tabBiasa = 'text-gray-600 hover:bg-gray-100';
        $waktu = fn ($t) => $t ? $t->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-';
    @endphp

    <div class="mb-4">
        <h1 class="text-xl font-bold">Histori Semua Domain</h1>
        <p class="text-xs text-gray-500">Data yang dihapus dari seluruh domain dan catatan siapa melakukan apa.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex gap-2 mb-4 text-sm">
        <a href="{{ route('superuser.histori-semua.index', array_filter(['domain' => $domain])) }}" class="px-4 py-2 rounded-md {{ $tab === 'data' ? $tabAktif : $tabBiasa }}">Data Terhapus</a>
        <a href="{{ route('superuser.histori-semua.index', array_filter(['tab' => 'log', 'domain' => $domain])) }}" class="px-4 py-2 rounded-md {{ $tab === 'log' ? $tabAktif : $tabBiasa }}">Log Aktivitas</a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $tab === 'log' ? 'Cari nama data atau pelaku...' : 'Cari nama data...' }}"
               class="border-gray-300 rounded-md text-sm flex-1 min-w-[200px]">
        <select name="domain" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua domain</option>
            @foreach($domains as $slug => $cfg)
                <option value="{{ $slug }}" @selected($domain === $slug)>{{ $cfg['label'] }}</option>
            @endforeach
        </select>
        @if($tab === 'log')
            <select name="aksi" class="border-gray-300 rounded-md text-sm">
                <option value="">Semua aksi</option>
                @foreach(['dihapus' => 'Dihapus', 'dipulihkan' => 'Dipulihkan', 'dihapus_permanen' => 'Dihapus permanen'] as $val => $lbl)
                    <option value="{{ $val }}" @selected(request('aksi') === $val)>{{ $lbl }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Filter</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            @if($tab === 'data')
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Domain</th>
                            <th class="px-3 py-2 text-left">Nama Data</th>
                            <th class="px-3 py-2 text-left">Provinsi</th>
                            <th class="px-3 py-2 text-left">Dihapus Oleh</th>
                            <th class="px-3 py-2 text-left">Dihapus Pada</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $it)
                            @php $row = $it['row']; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap">{{ $it['label'] }}</td>
                                <td class="px-3 py-2 font-medium">{{ $row->nama_objek }}</td>
                                <td class="px-3 py-2">{{ $row->provinsi->nama_provinsi ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $row->deletedBy->name ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $waktu($row->deleted_at) }}</td>
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <form method="POST" action="{{ route($it['route'] . '.histori.restore', $row->id) }}" class="inline"
                                          onsubmit="return confirm('Pulihkan data ini?')">
                                        @csrf
                                        <button type="submit" class="text-green-700 hover:underline mr-3">Pulihkan</button>
                                    </form>
                                    <form method="POST" action="{{ route($it['route'] . '.histori.force', $row->id) }}" class="inline"
                                          onsubmit="return confirm('HAPUS PERMANEN data ini? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus Permanen</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-6 text-gray-400">Tidak ada data terhapus</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Waktu</th>
                            <th class="px-3 py-2 text-left">Domain</th>
                            <th class="px-3 py-2 text-left">Aksi</th>
                            <th class="px-3 py-2 text-left">Nama Data</th>
                            <th class="px-3 py-2 text-left">Oleh</th>
                            <th class="px-3 py-2 text-left">Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap">{{ $waktu($row->created_at) }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $domains[$row->domain]['label'] ?? $row->domain }}</td>
                                <td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium {{ $badge[$row->aksi] ?? 'bg-gray-100 text-gray-700' }}">{{ str_replace('_', ' ', $row->aksi) }}</span></td>
                                <td class="px-3 py-2 font-medium">{{ $row->nama_data }}</td>
                                <td class="px-3 py-2">{{ $row->user_nama ?? '-' }}</td>
                                <td class="px-3 py-2">{{ str_replace('_', ' ', $row->user_role ?? '-') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-6 text-gray-400">Belum ada aktivitas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</x-layouts.admin>
