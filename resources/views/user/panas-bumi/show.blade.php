<x-layouts.user :title="$panasBumi->nama_objek">
    <div class="max-w-2xl">
        <div class="bg-white border rounded-xl p-6 space-y-6">
            <h1 class="text-xl font-bold">{{ $panasBumi->nama_objek }}</h1>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Info Umum</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Provinsi</span>{{ $panasBumi->provinsi->nama_provinsi }}</div>
                    <div><span class="text-gray-500 block text-xs">Kabupaten</span>{{ $panasBumi->kabupaten->nama_kabupaten }}</div>
                    <div><span class="text-gray-500 block text-xs">Status</span>{{ $panasBumi->statDikBb->label }}</div>
                    <div><span class="text-gray-500 block text-xs">Kapasitas Terpasang</span>{{ number_format($panasBumi->kapasitas_terpasang, 2) }} MWe</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Data</span>{{ $panasBumi->tahun_data }}</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Neraca</span>{{ $panasBumi->tahun_neraca }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Sumber Daya (MWe)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Spekulatif</span>{{ number_format($panasBumi->spekulatif, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Hipotetis</span>{{ number_format($panasBumi->hipotetis, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Terduga</span>{{ number_format($panasBumi->terduga, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Total</span>{{ number_format($panasBumi->total_sd, 3) }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Cadangan & Kapasitas (MWe)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Cadangan Terbukti</span>{{ number_format($panasBumi->terbukti, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Kapasitas Terpasang</span>{{ number_format($panasBumi->kapasitas_terpasang, 3) }}</div>
                </div>
            </div>

            @if($panasBumi->remark)
            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Catatan</h3>
                <p class="text-sm text-gray-600">{{ $panasBumi->remark }}</p>
            </div>
            @endif

            <a href="{{ route('user.panas-bumi.index') }}" class="inline-block text-sm text-gray-500">← Kembali</a>
        </div>
    </div>
</x-layouts.user>