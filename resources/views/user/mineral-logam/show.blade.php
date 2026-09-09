<x-layouts.user :title="$mineralLogam->komoditasLogam->nama_komoditas">
    <div class="max-w-2xl">
        <div class="bg-white border rounded-xl p-6 space-y-6">
            <h1 class="text-xl font-bold">{{ $mineralLogam->komoditasLogam->nama_komoditas }}</h1>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Info Umum</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Komoditas</span>{{ $mineralLogam->komoditasLogam->nama_komoditas }}</div>
                    <div><span class="text-gray-500 block text-xs">Kelompok</span>{{ $mineralLogam->komoditasLogam->kelompokKomoditasLogam->nama_kelompok }}</div>
                    <div><span class="text-gray-500 block text-xs">Provinsi</span>{{ $mineralLogam->provinsi->nama_provinsi }}</div>
                    <div><span class="text-gray-500 block text-xs">Kabupaten</span>{{ $mineralLogam->kabupaten->nama_kabupaten }}</div>
                    <div><span class="text-gray-500 block text-xs">Status</span>{{ $mineralLogam->statDikBb->label }}</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Data</span>{{ $mineralLogam->tahun_data }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Sumber Daya (bijih / logam, Ton)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Tereka</span>{{ number_format($mineralLogam->tereka_bijih, 3) }} / {{ number_format($mineralLogam->tereka_logam, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Tertunjuk</span>{{ number_format($mineralLogam->tertunjuk_bijih, 3) }} / {{ number_format($mineralLogam->tertunjuk_logam, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Terukur</span>{{ number_format($mineralLogam->terukur_bijih, 3) }} / {{ number_format($mineralLogam->terukur_logam, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Total</span>{{ number_format($mineralLogam->total_sd_bijih, 3) }} / {{ number_format($mineralLogam->total_sd_logam, 3) }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Cadangan (bijih / logam, Ton)</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Terkira</span>{{ number_format($mineralLogam->terkira_bijih, 3) }} / {{ number_format($mineralLogam->terkira_logam, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Terbukti</span>{{ number_format($mineralLogam->terbukti_bijih, 3) }} / {{ number_format($mineralLogam->terbukti_logam, 3) }}</div>
                    <div><span class="text-gray-500 block text-xs">Total</span>{{ number_format($mineralLogam->total_cad_bijih, 3) }} / {{ number_format($mineralLogam->total_cad_logam, 3) }}</div>
                </div>
            </div>

            @if($mineralLogam->remark)
            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Catatan</h3>
                <p class="text-sm text-gray-600">{{ $mineralLogam->remark }}</p>
            </div>
            @endif

            <a href="{{ route('user.mineral-logam.index') }}" class="inline-block text-sm text-gray-500">← Kembali</a>
        </div>
    </div>
</x-layouts.user>