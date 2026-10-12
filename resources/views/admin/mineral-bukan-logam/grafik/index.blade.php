<x-layouts.admin title="Grafik dan Visualisasi">
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

    @php
    $ringkasan = [
        ['label' => 'Total Sumber Daya', 'nilai' => $totalSd, 'satuan' => 'ton'],
        ['label' => 'Total Cadangan', 'nilai' => $totalCad, 'satuan' => 'ton'],
    ];
@endphp
<x-ringkasan-angka :items="$ringkasan" />

    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
        <div class="text-sm font-semibold mb-4">
            @if($komoditasTerpilih)
                Total Sumber Daya per Provinsi — {{ $komoditasTerpilih->nama_komoditas }} (Ton)
            @else
                Total Sumber Daya per Komoditas (Ton)
            @endif
        </div>
        @php
    $dataBar = $perProvinsi->map(fn ($row) => ['label' => (string) ($komoditasTerpilih ? ($row->provinsi->nama_provinsi ?? '-') : ($row->komoditasBukanLogam->nama_komoditas ?? '-')), 'value' => (float) $row->total])->all();
@endphp
<x-grafik-bar :data="$dataBar" :maks-batang="10" satuan="ton" />
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
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Sumber Daya {{ $pSd }}% · {{ \App\Support\Angka::lengkap($totalSd) }} ton</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#4A6FE3]"></span>Cadangan {{ $pCad }}% · {{ \App\Support\Angka::lengkap($totalCad) }} ton</div>
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
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#B0B0B0]"></span>Hipotetik {{ $pHip }}% · {{ \App\Support\Angka::lengkap($totalHipotetik) }} ton</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Tereka {{ $pTereka }}% · {{ \App\Support\Angka::lengkap($totalTereka) }} ton</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#E08C2B]"></span>Tertunjuk {{ $pTertunjuk }}% · {{ \App\Support\Angka::lengkap($totalTertunjuk) }} ton</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#4A6FE3]"></span>Terukur {{ $pTerukur }}% · {{ \App\Support\Angka::lengkap($totalTerukur) }} ton</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Tren per Tahun Data</div>
            @php
    $dataTren = $trendPerTahun->map(fn ($t) => ['label' => (string) $t->tahun_data, 'value' => (float) $t->total])->all();
@endphp
<x-grafik-bar :data="$dataTren" satuan="ton" :tinggi="130" warna-akhir="#4A6FE3" />
        </div>
    </div>
    <x-tabel-referensi-domain domain="mineral_bukan_logam" />
</x-layouts.admin>
