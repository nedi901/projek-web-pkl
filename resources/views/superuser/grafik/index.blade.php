<x-layouts.superuser title="Grafik dan Visualisasi">
    <h1 class="text-xl font-bold mb-1">Grafik dan Visualisasi</h1>
    <p class="text-xs text-gray-500 mb-4">Perbandingan Antar Provinsi dan Tren Tahunan — Batubara</p>

    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
        <div class="text-sm font-semibold mb-4">Total Sumber Daya per Provinsi (Juta Ton)</div>
        @if($perProvinsi->count())
            @php $max = $perProvinsi->max('total') ?: 1; @endphp
            <div class="flex items-end gap-4 h-44">
                @foreach($perProvinsi as $row)
                    <div class="flex-1 bg-[#F2C230] rounded-t" style="height: {{ ($row->total / $max) * 100 }}%"></div>
                @endforeach
            </div>
            <div class="flex gap-4 mt-2 text-[10px] text-gray-400">
                @foreach($perProvinsi as $row)
                    <div class="flex-1 text-center">{{ $row->provinsi->nama_provinsi ?? '-' }}</div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 text-sm py-10">Belum ada data</div>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Proporsi Klasifikasi SNI</div>
            @php
                $pTereka = round($totalTereka / $totalKlasifikasi * 100);
                $pTertunjuk = round($totalTertunjuk / $totalKlasifikasi * 100);
                $pTerukur = max(0, 100 - $pTereka - $pTertunjuk);
            @endphp
            <div class="flex items-center justify-center">
                <div class="w-28 h-28 rounded-full"
                     style="background: conic-gradient(#F2C230 0% {{ $pTereka }}%, #E08C2B {{ $pTereka }}% {{ $pTereka + $pTertunjuk }}%, #B0B0B0 {{ $pTereka + $pTertunjuk }}% 100%);">
                    <div class="w-14 h-14 bg-white rounded-full m-7"></div>
                </div>
            </div>
            <div class="flex flex-col gap-1 mt-3 text-[11px]">
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Tereka {{ $pTereka }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#E08C2B]"></span>Tertunjuk {{ $pTertunjuk }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#B0B0B0]"></span>Terukur {{ $pTerukur }}%</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Tren per Tahun Data</div>
            @if($trendPerTahun->count())
                @php $tahunTerakhir = $trendPerTahun->last()->tahun_data; @endphp
                <div class="flex items-end gap-3 h-32">
                    @foreach($trendPerTahun as $t)
                        <div class="flex-1 flex flex-col items-center justify-end h-full">
                            @if($t->tahun_data == $tahunTerakhir)
                                <span class="text-[9px] text-orange-600 font-semibold mb-1">Berjalan</span>
                            @endif
                            <div class="w-full {{ $t->tahun_data == $tahunTerakhir ? 'bg-[#4A6FE3]' : 'bg-[#F2C230]' }} rounded-t"
                                 style="height: {{ ($t->total / $trendMax) * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-3 mt-2 text-[10px] text-gray-400">
                    @foreach($trendPerTahun as $t)
                        <div class="flex-1 text-center">{{ $t->tahun_data }}</div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-400 text-sm py-10">Belum ada data</div>
            @endif
        </div>
    </div>
</x-layouts.superuser>