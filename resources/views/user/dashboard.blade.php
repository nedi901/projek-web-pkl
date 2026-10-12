@php
    $warna = [
        'mineral_logam' => '#F2C230',
        'mineral_bukan_logam' => '#E08C2B',
        'batubara' => '#1a1a1a',
        'panas_bumi' => '#4A6FE3',
        'gambut' => '#7A9A4B',
    ];
    $labelTahun = $tahun ? (string) $tahun : '2024–2025';
@endphp

<x-layouts.user title="Beranda">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-xl font-bold">Beranda</h1>
            <p class="text-xs text-gray-500 mt-0.5">Informasi Neraca Sumber Daya Mineral, Batubara, Panas Bumi, dan Gambut Indonesia</p>
        </div>
        <form method="GET">
            <label class="text-[10px] text-gray-400 block mb-1">Tahun Data</label>
            <select name="tahun" onchange="this.form.submit()" class="border border-gray-300 rounded-md text-sm px-2 py-1">
                <option value="" @selected(! $tahun)>2024–2025</option>
                <option value="2024" @selected($tahun === 2024)>2024</option>
                <option value="2025" @selected($tahun === 2025)>2025</option>
            </select>
        </form>
    </div>

    {{-- Kartu ringkasan: tiap domain pakai satuannya sendiri --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        @foreach($domains as $d)
            @php
                [$angka, $satuan] = \App\Support\Angka::format($d['total_sd'], $d['satuan']);
                $kartu = 'bg-white border border-gray-200 rounded-xl p-4 block';
            @endphp
            @if($d['route'])
                <a href="{{ route($d['route']) }}" class="{{ $kartu }} hover:border-[#F2C230]">
            @else
                <div class="{{ $kartu }}">
            @endif
                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-2">
                    <span class="w-2 h-2 rounded-sm" style="background: {{ $warna[$d['key']] }}"></span>
                    Sumber Daya<br>{{ $d['label'] }}
                </div>
                <div class="text-lg font-bold leading-tight break-all">{{ \App\Support\Angka::lengkap($d['total_sd']) }}</div>
                <div class="text-xs font-medium text-gray-600">{{ $d['satuan'] }}@if($d['satuan'] === 'ton') · ≈ {{ $angka }} {{ $satuan }}@endif</div>
                <div class="text-[11px] text-gray-500 mt-2 break-all">
                    @if($d['total_cad'] !== null)
                        Cadangan: {{ \App\Support\Angka::lengkap($d['total_cad']) }} {{ $d['satuan'] }}
                    @else
                        Belum ada data cadangan
                    @endif
                </div>
                <div class="text-[10px] text-gray-400">{{ number_format($d['jumlah'], 0, ',', '.') }} data · {{ $labelTahun }}</div>
            @if($d['route'])
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        {{-- Sumber Daya vs Cadangan, dibandingkan DI DALAM tiap domain saja --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-1">Sumber Daya vs Cadangan per Domain</div>
            <div class="text-[11px] text-gray-400 mb-4">Proporsi dihitung di dalam tiap domain (satuan tiap domain berbeda, jadi tidak dibandingkan antar domain).</div>

            <div class="space-y-4">
                @foreach($domains as $d)
                    @if($d['total_cad'] !== null)
                        @php
                            $gab = max($d['total_sd'] + $d['total_cad'], 1);
                            $pSd = round($d['total_sd'] / $gab * 100);
                            $pCad = 100 - $pSd;
                        @endphp
                        <div>
                            <div class="flex justify-between text-[11px] mb-1">
                                <span class="font-medium">{{ $d['label'] }}</span>
                                <span class="text-gray-400">SD {{ $pSd }}% · Cadangan {{ $pCad }}%</span>
                            </div>
                            <div class="flex h-3 rounded overflow-hidden bg-gray-100">
                                <div class="bg-[#F2C230]" style="width: {{ $pSd }}%"></div>
                                <div class="bg-[#4A6FE3]" style="width: {{ $pCad }}%"></div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="flex gap-4 mt-4 text-[11px]">
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Sumber Daya</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#4A6FE3]"></span>Cadangan</div>
            </div>
        </div>

        {{-- Jumlah data: satu-satunya ukuran yang bebas satuan --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-1">Jumlah Data Neraca per Domain</div>
            <div class="text-[11px] text-gray-400 mb-4">Total {{ number_format($jumlahTotal, 0, ',', '.') }} data untuk tahun {{ $labelTahun }}.</div>

            <div class="space-y-3">
                @foreach($domains as $d)
                    <div>
                        <div class="flex justify-between text-[11px] mb-1">
                            <span class="font-medium">{{ $d['label'] }}</span>
                            <span class="text-gray-500">{{ number_format($d['jumlah'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-3 rounded bg-gray-100 overflow-hidden">
                            <div class="h-3 rounded" style="width: {{ $d['jumlah'] / $jumlahMax * 100 }}%; background: {{ $warna[$d['key']] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 flex justify-between items-center gap-4">
        <div>
            <div class="text-sm font-semibold">Lihat Grafik, Tabel, dan Data Lengkap</div>
            <div class="text-xs text-gray-500 mt-0.5">Telusuri neraca per provinsi dan komoditas, lengkap dengan tabel rekapitulasi dari buku.</div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('user.grafik.index') }}" class="border border-gray-300 text-xs font-semibold px-4 py-2 rounded-md">Grafik &amp; Tabel</a>
            <a href="{{ route('user.batubara.index') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-xs font-semibold px-4 py-2 rounded-md">Buka Data →</a>
        </div>
    </div>

    <p class="text-[10px] text-gray-400 mt-3">
        Angka adalah penjumlahan data neraca yang tersimpan di aplikasi untuk tahun terpilih. Sebagian data berupa rekap agregat per provinsi
        (bukan per titik lokasi). Bijih Mineral Logam dijumlahkan dalam ton; beberapa komoditas (mis. timah, emas aluvial) aslinya dalam m³.
    </p>
</x-layouts.user>
