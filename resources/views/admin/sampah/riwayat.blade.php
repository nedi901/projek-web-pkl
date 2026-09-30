<x-layouts.admin :title="'Riwayat ' . $label">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold">Riwayat Aktivitas — {{ $label }}</h1>
            <p class="text-xs text-gray-500">Catatan siapa yang menghapus, memulihkan, atau menghapus permanen data.</p>
        </div>
        <a href="{{ route($routePrefix . '.sampah.index') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">
            ← Kembali ke Sampah
        </a>
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <select name="aksi" class="border-gray-300 rounded-md text-sm">
            <option value="">Semua aksi</option>
            @foreach(['dihapus' => 'Dihapus', 'dipulihkan' => 'Dipulihkan', 'dihapus_permanen' => 'Dihapus permanen'] as $val => $lbl)
                <option value="{{ $val }}" @selected(request('aksi') === $val)>{{ $lbl }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">Filter</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-2 text-left">Waktu</th>
                        <th class="px-3 py-2 text-left">Aksi</th>
                        <th class="px-3 py-2 text-left">Nama Data</th>
                        <th class="px-3 py-2 text-left">Oleh</th>
                        <th class="px-3 py-2 text-left">Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                    @php
                        $badge = match ($row->aksi) {
                            'dihapus' => 'bg-yellow-100 text-yellow-800',
                            'dipulihkan' => 'bg-green-100 text-green-800',
                            'dihapus_permanen' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap">{{ $row->created_at?->format('d M Y, H:i') }}</td>
                        <td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">{{ str_replace('_', ' ', $row->aksi) }}</span></td>
                        <td class="px-3 py-2 font-medium">{{ $row->nama_data }}</td>
                        <td class="px-3 py-2">{{ $row->user_nama ?? '-' }}</td>
                        <td class="px-3 py-2">{{ str_replace('_', ' ', $row->user_role ?? '-') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-6 text-gray-400">Belum ada aktivitas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $data->links() }}</div>
</x-layouts.admin>
