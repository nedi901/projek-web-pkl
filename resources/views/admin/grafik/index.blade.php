<x-layouts.admin title="Grafik dan Visualisasi">
    <h1 class="text-xl font-bold mb-1">Grafik dan Visualisasi</h1>
    <p class="text-xs text-gray-500 mb-4">Perbandingan Antar Provinsi dan Tren Tahunan — Batubara</p>

    @php
    $ringkasan = [
        ['label' => 'Total Sumber Daya Batubara', 'nilai' => $totalSd, 'satuan' => 'ton'],
        ['label' => 'Total Cadangan Batubara', 'nilai' => $totalCad, 'satuan' => 'ton'],
        ['label' => 'Jumlah Data Neraca', 'nilai' => $jumlahData, 'satuan' => 'data', 'desimal' => 0],
    ];
@endphp
<x-ringkasan-angka :items="$ringkasan" />

    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
        <div class="text-sm font-semibold mb-4">Total Sumber Daya per Provinsi (Ton)</div>
        @php
    $dataBar = $perProvinsi->map(fn ($row) => ['label' => (string) ($row->provinsi->nama_provinsi ?? '-'), 'value' => (float) $row->total])->all();
@endphp
<x-grafik-bar :data="$dataBar" :maks-batang="10" satuan="ton" />
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
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Tereka {{ $pTereka }}% · {{ \App\Support\Angka::lengkap($totalTereka) }} ton</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#E08C2B]"></span>Tertunjuk {{ $pTertunjuk }}% · {{ \App\Support\Angka::lengkap($totalTertunjuk) }} ton</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#B0B0B0]"></span>Terukur {{ $pTerukur }}% · {{ \App\Support\Angka::lengkap($totalTerukur) }} ton</div>
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
    <x-tabel-referensi-domain domain="batubara" />
</x-layouts.admin>