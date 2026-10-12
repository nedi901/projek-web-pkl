<x-layouts.user :title="$gambut->nama_objek">
    <div class="max-w-2xl">
        <div class="bg-white border rounded-xl p-6 space-y-6">
            <h1 class="text-xl font-bold">{{ $gambut->nama_objek }}</h1>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Info Umum</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Provinsi</span>{{ $gambut->provinsi->nama_provinsi ?? '-' }}</div>
                    <div><span class="text-gray-500 block text-xs">Kabupaten</span>{{ $gambut->kabupaten->nama_kabupaten ?? '-' }}</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Data</span>{{ $gambut->tahun_data }}</div>
                    <div><span class="text-gray-500 block text-xs">Tahun Neraca</span>{{ $gambut->tahun_neraca }}</div>
                </div>
            </div>

            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Kualitas &amp; Sumber Daya</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500 block text-xs">Nilai Kalori (adb)</span>{{ $gambut->nilai_kalori_label }}</div>
                    <div><span class="text-gray-500 block text-xs">Luas</span>{{ number_format($gambut->luas_ha, 2, ',', '.') }} ha</div>
                    <div><span class="text-gray-500 block text-xs">Volume</span>{{ number_format($gambut->volume_juta_m3, 2, ',', '.') }} juta m³</div>
                    <div><span class="text-gray-500 block text-xs">Total Sumber Daya</span>{{ number_format($gambut->total_sd, 2, ',', '.') }} juta ton</div>
                </div>
            </div>

            @if($gambut->remark)
            <div>
                <h3 class="text-xs uppercase text-orange-700 font-mono mb-2">Catatan</h3>
                <p class="text-sm text-gray-600">{{ $gambut->remark }}</p>
            </div>
            @endif

            <a href="{{ route('user.gambut.index') }}" class="inline-block text-sm text-gray-500">← Kembali</a>
        </div>
    </div>
</x-layouts.user>
