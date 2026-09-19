<x-layouts.superuser title="Grafik dan Visualisasi">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold">Grafik dan Visualisasi</h1>
            <p class="text-xs text-gray-500">
                @if($komoditasTerpilih)
                    {{ $komoditasTerpilih->nama_komoditas }} — Sumber Daya & Cadangan
                @else
                    Perbandingan Antar Komoditas — Mineral Bukan Logam
                @endif
            </p>
        </div>
        <form method="GET">
            <select name="komoditas_id" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm px-3 py-2">
                <option value="">Semua Komoditas</option>
                @foreach($komoditasList as $k)
                    <option value="{{ $k->id }}" @selected($komoditasId == $k->id)>{{ $k->nama_komoditas }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
        <div class="text-sm font-semibold mb-4">
            @if($komoditasTerpilih)
                Total Sumber Daya per Provinsi — {{ $komoditasTerpilih->nama_komoditas }} (Ton)
            @else
                Total Sumber Daya per Komoditas (Ton)
            @endif
        </div>
        @if($perProvinsi->count())
            @php $max = $perProvinsi->max('total') ?: 1; @endphp
            <div class="flex items-end gap-4 h-44">
                @foreach($perProvinsi as $row)
                    <div class="flex-1 bg-[#F2C230] rounded-t" style="height: {{ ($row->total / $max) * 100 }}%"></div>
                @endforeach
            </div>
            <div class="flex gap-4 mt-2 text-[10px] text-gray-400">
                @foreach($perProvinsi as $row)
                    <div class="flex-1 text-center">
                        {{ $komoditasTerpilih ? ($row->provinsi->nama_provinsi ?? '-') : ($row->komoditasBukanLogam->nama_komoditas ?? '-') }}
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 text-sm py-10">Belum ada data</div>
        @endif
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Sumber Daya vs Cadangan</div>
            @php
                $pSd = round($totalSd / $totalGabungan * 100);
                $pCad = max(0, 100 - $pSd);
            @endphp
            <div class="flex items-center justify-center">
                <div class="w-24 h-24 rounded-full"
                     style="background: conic-gradient(#F2C230 0% {{ $pSd }}%, #4A6FE3 {{ $pSd }}% 100%);">
                    <div class="w-12 h-12 bg-white rounded-full m-6"></div>
                </div>
            </div>
            <div class="flex flex-col gap-1 mt-3 text-[11px]">
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Sumber Daya {{ $pSd }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#4A6FE3]"></span>Cadangan {{ $pCad }}%</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Proporsi Klasifikasi SNI</div>
            @php
                $pHip = round($totalHipotetik / $totalKlasifikasi * 100);
                $pTereka = round($totalTereka / $totalKlasifikasi * 100);
                $pTertunjuk = round($totalTertunjuk / $totalKlasifikasi * 100);
                $pTerukur = max(0, 100 - $pHip - $pTereka - $pTertunjuk);
            @endphp
            <div class="flex items-center justify-center">
                <div class="w-24 h-24 rounded-full"
                     style="background: conic-gradient(#B0B0B0 0% {{ $pHip }}%, #F2C230 {{ $pHip }}% {{ $pHip+$pTereka }}%, #E08C2B {{ $pHip+$pTereka }}% {{ $pHip+$pTereka+$pTertunjuk }}%, #4A6FE3 {{ $pHip+$pTereka+$pTertunjuk }}% 100%);">
                    <div class="w-12 h-12 bg-white rounded-full m-6"></div>
                </div>
            </div>
            <div class="flex flex-col gap-1 mt-3 text-[10px]">
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#B0B0B0]"></span>Hipotetik {{ $pHip }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Tereka {{ $pTereka }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#E08C2B]"></span>Tertunjuk {{ $pTertunjuk }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#4A6FE3]"></span>Terukur {{ $pTerukur }}%</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Tren per Tahun Data</div>
            @if($trendPerTahun->count())
                @php $tahunTerakhir = $trendPerTahun->last()->tahun_data; @endphp
                <div class="flex items-end gap-2 h-24">
                    @foreach($trendPerTahun as $t)
                        <div class="flex-1 flex flex-col items-center justify-end h-full">
                            <div class="w-full {{ $t->tahun_data == $tahunTerakhir ? 'bg-[#4A6FE3]' : 'bg-[#F2C230]' }} rounded-t"
                                 style="height: {{ ($t->total / $trendMax) * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-2 mt-2 text-[9px] text-gray-400">
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
