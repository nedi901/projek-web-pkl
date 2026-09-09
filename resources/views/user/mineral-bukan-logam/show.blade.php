<x-layouts.user :title="$mineralBukanLogam->komoditasBukanLogam->nama_komoditas">
    <div class="max-w-2xl">
        <div class="bg-white border rounded-xl p-6 space-y-6">
            <h1 class="text-xl font-bold">{{ $mineralBukanLogam->komoditasBukanLogam->nama_komoditas }}</h1>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Info Umum</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Komoditas</span>{{ $mineralBukanLogam->komoditasBukanLogam->nama_komoditas }}</div>
                    <div><span class="text-gray-500 block text-xs">Kelompok</span>{{ $mineralBukanLogam->komoditasBukanLogam->kelompokKomoditasBukanLogam->nama_kelompok }}</div>
                    <div><span class="text-gray-500 block text-xs">Provinsi</span>{{ $mineralBukanLogam->provinsi->nama_provinsi }}</div>
                    <div><span class="text-gray-500 block text-xs">Kabupaten</span>{{ $mineralBukanLogam->kabupaten->nama_kabupaten }}</div>
                    <div><span class="text-gray-500 block text-xs">Status</span>{{ $mineralBukanLogam->statDikBb->label }}</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Data</span>{{ $mineralBukanLogam->tahun_data }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Sumber Daya (Ton/m³)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Tereka</span>{{ number_format($mineralBukanLogam->tereka, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Tertunjuk</span>{{ number_format($mineralBukanLogam->tertunjuk, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Terukur</span>{{ number_format($mineralBukanLogam->terukur, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Total</span>{{ number_format($mineralBukanLogam->total_sd, 3) }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Cadangan (Ton/m³)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Terkira</span>{{ number_format($mineralBukanLogam->terkira, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Terbukti</span>{{ number_format($mineralBukanLogam->terbukti, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Total</span>{{ number_format($mineralBukanLogam->total_cad, 3) }}</div>
                </div>
            </div>

            @if($mineralBukanLogam->remark)
            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Catatan</h3>
                <p class="text-sm text-gray-600">{{ $mineralBukanLogam->remark }}</p>
            </div>
            @endif

            <a href="{{ route('user.mineral-bukan-logam.index') }}" class="inline-block text-sm text-gray-500">← Kembali</a>
        </div>
    </div>
</x-layouts.user>