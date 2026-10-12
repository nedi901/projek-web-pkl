<x-layouts.user title="Grafik dan Visualisasi">
    <h1 class="text-xl font-bold mb-1">Grafik dan Visualisasi</h1>
    <p class="text-xs text-gray-500 mb-4">Perbandingan Antar Provinsi dan Tren Tahunan</p>

    <form method="GET" class="flex flex-wrap gap-4 mb-6">
        <div>
            <label class="text-[10px] text-gray-400 block mb-1">Provinsi</label>
            <select name="provinsi_id" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm px-2 py-1.5">
                <option value="">Semua Provinsi</option>
                @foreach($provinsis as $p)
                    <option value="{{ $p->id }}" @selected(request('provinsi_id')==$p->id)>{{ $p->nama_provinsi }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-[10px] text-gray-400 block mb-1">Domain</label>
            <select name="domain" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm px-2 py-1.5">
                <option value="batubara" @selected($domain=='batubara')>Batubara</option>
                <option value="mineral-logam" @selected($domain=='mineral-logam')>Mineral Logam</option>
                <option value="mineral-bukan-logam" @selected($domain=='mineral-bukan-logam')>Bukan Logam & Batuan</option>
                <option value="panas-bumi" @selected($domain=='panas-bumi')>Panas Bumi</option>
                <option value="gambut" @selected($domain=='gambut')>Gambut</option>
            </select>
        </div>
    </form>

    @php
    $ringkasan = [
        ['label' => 'Total Sumber Daya', 'nilai' => $totalSd, 'satuan' => $satuanGrafik],
        ['label' => 'Total Cadangan', 'nilai' => $totalCad, 'satuan' => $satuanGrafik],
        ['label' => 'Jumlah Data Neraca', 'nilai' => $jumlahData, 'satuan' => 'data', 'desimal' => 0],
    ];
@endphp
<x-ringkasan-angka :items="$ringkasan" />

    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-4">
        <div class="text-sm font-semibold mb-4">Total Sumber Daya per Provinsi ({{ $satuanGrafik }})</div>
        @php
    $dataBar = $perProvinsi->map(fn ($row) => ['label' => (string) ($row->provinsi->nama_provinsi ?? '-'), 'value' => (float) $row->total])->all();
@endphp
<x-grafik-bar :data="$dataBar" :maks-batang="10" :satuan="$satuanGrafik" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        @if($domain !== 'gambut')
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
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Tereka {{ $pTereka }}% · {{ \App\Support\Angka::lengkap($totalTereka) }} {{ $satuanGrafik }}</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#E08C2B]"></span>Tertunjuk {{ $pTertunjuk }}% · {{ \App\Support\Angka::lengkap($totalTertunjuk) }} {{ $satuanGrafik }}</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#B0B0B0]"></span>Terukur {{ $pTerukur }}% · {{ \App\Support\Angka::lengkap($totalTerukur) }} {{ $satuanGrafik }}</div>
            </div>
        </div>

        @endif

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Tren per Tahun Neraca</div>
            @php
    $dataTren = $trendPerTahun->map(fn ($t) => ['label' => (string) $t->tahun_data, 'value' => (float) $t->total])->all();
@endphp
<x-grafik-bar :data="$dataTren" :satuan="$satuanGrafik" :tinggi="130" warna-akhir="#4A6FE3" />
        </div>
    </div>
    <x-tabel-referensi-domain :domain="str_replace('-', '_', $domain)" />
</x-layouts.user>
