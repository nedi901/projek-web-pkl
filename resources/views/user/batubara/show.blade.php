<x-layouts.user :title="$batubara->nama_objek">
    <div class="max-w-2xl">
        <div class="bg-white border rounded-xl p-6 space-y-6">
            <h1 class="text-xl font-bold">{{ $batubara->nama_objek }}</h1>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Info Umum</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Provinsi</span>{{ $batubara->provinsi->nama_provinsi }}</div>
                    <div><span class="text-gray-500 block text-xs">Kabupaten</span>{{ $batubara->kabupaten->nama_kabupaten }}</div>
                    <div><span class="text-gray-500 block text-xs">Kelas Kalori</span>{{ $batubara->kelasKalori->label }}</div>
                    <div><span class="text-gray-500 block text-xs">Status</span>{{ $batubara->statDikBb->label }}</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Data</span>{{ $batubara->tahun_data }}</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Neraca</span>{{ $batubara->tahun_neraca }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Sumber Daya (Juta Ton)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Tereka</span>{{ number_format($batubara->tereka, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Tertunjuk</span>{{ number_format($batubara->tertunjuk, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Terukur</span>{{ number_format($batubara->terukur, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Total</span>{{ number_format($batubara->total_sd, 3) }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Cadangan (Juta Ton)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Terkira</span>{{ number_format($batubara->terkira, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Terbukti</span>{{ number_format($batubara->terbukti, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Total</span>{{ number_format($batubara->total_cad, 3) }}</div>
                </div>
            </div>

            @if($batubara->remark)
            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Catatan</h3>
                <p class="text-sm text-gray-600">{{ $batubara->remark }}</p>
            </div>
            @endif

            <a href="{{ route('user.batubara.index') }}" class="inline-block text-sm text-gray-500">← Kembali</a>
        </div>
    </div>
</x-layouts.user>