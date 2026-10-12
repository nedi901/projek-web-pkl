{{--
  Grafik batang dengan SUMBU Y (angka skala di kiri + garis bantu) dan nilai di atas tiap batang.
  Props:
    data       : array of ['label' => string, 'value' => number, 'warna' => '#hex' (opsional)]
    maksBatang : (opsional) kalau data lebih banyak dari ini, grafik hanya menampilkan (maksBatang - 1) nilai teratas
                 + 1 batang abu-abu "Lainnya"; tabel angka lengkap di bawah tetap memuat SEMUA baris
    satuan  : teks satuan (mis. 'ton', 'MWe', 'juta ton') -> jadi judul sumbu Y
    tinggi  : tinggi area batang dalam px (default 180)
    desimal : jumlah desimal untuk angka lengkap di tabel bawah (default 2)
--}}
@props(['data' => [], 'satuan' => '', 'tinggi' => 180, 'desimal' => 2, 'warna' => '#F2C230', 'warnaAkhir' => null, 'maksBatang' => null])

@php
    $semua = collect($data)->values();
    $rows = $semua;
    $dipangkas = false;
    $sisa = 0;

    if ($maksBatang && $semua->count() > $maksBatang) {
        $urut = $semua->sortByDesc('value')->values();
        $teratas = $urut->take($maksBatang - 1);
        $lain = $urut->slice($maksBatang - 1);
        $sisa = $lain->count();
        $rows = $teratas->push(['label' => "Lainnya ({$sisa})", 'value' => (float) $lain->sum('value'), 'warna' => '#9CA3AF'])->values();
        $dipangkas = true;
    }

    $maks = (float) ($rows->max('value') ?: 0);

    // batas atas "bulat" supaya skala sumbu enak dibaca: 1 / 2 / 5 / 10 x 10^n, dibagi 5 selang
    if ($maks <= 0) {
        $atas = 1.0;
    } else {
        $mag = 10 ** floor(log10($maks));
        $norm = $maks / $mag;
        $atas = $mag * ($norm <= 1 ? 1 : ($norm <= 2 ? 2 : ($norm <= 5 ? 5 : 10)));
    }
    $ticks = [];
    for ($i = 5; $i >= 0; $i--) {
        $ticks[] = $atas * $i / 5;
    }
    $n = max($rows->count(), 1);
@endphp

@if($rows->isEmpty())
    <div class="text-center text-gray-400 text-sm py-10">Belum ada data</div>
@else
    <div class="flex">
        {{-- judul sumbu Y (vertikal) --}}
        <div class="flex items-center justify-center w-5 shrink-0">
            <span class="text-[10px] text-gray-500 whitespace-nowrap" style="writing-mode: vertical-rl; transform: rotate(180deg);">
                {{ $satuan }}
            </span>
        </div>

        {{-- angka skala sumbu Y --}}
        <div class="relative w-20 shrink-0 text-right pr-2" style="height: {{ $tinggi }}px;">
            @foreach($ticks as $i => $t)
                <span class="absolute right-2 text-[10px] text-gray-500 leading-none"
                      style="top: {{ $i * 20 }}%; transform: translateY(-50%);">{{ \App\Support\Angka::sumbu($t) }}</span>
            @endforeach
        </div>

        {{-- area plot: garis bantu + batang --}}
        <div class="relative flex-1 border-l border-b border-gray-300" style="height: {{ $tinggi }}px;">
            @foreach($ticks as $i => $t)
                @if($i > 0 && $i < 5)
                    <div class="absolute left-0 right-0 border-t border-dashed border-gray-200" style="top: {{ $i * 20 }}%;"></div>
                @endif
            @endforeach

            <div class="absolute inset-0 flex items-end gap-3 px-3">
                @foreach($rows as $idx => $row)
                    @php
                        $v = (float) $row['value'];
                        $persen = $atas > 0 ? min(100, $v / $atas * 100) : 0;
                        $isAkhir = $warnaAkhir && $idx === $rows->count() - 1;
                        $w = $isAkhir ? $warnaAkhir : ($row['warna'] ?? $warna);
                    @endphp
                    <div class="flex-1 h-full flex flex-col justify-end items-center min-w-0" title="{{ $row['label'] }}: {{ \App\Support\Angka::lengkap($v, $desimal) }} {{ $satuan }}">
                        <span class="text-[9px] text-gray-600 mb-0.5 leading-none whitespace-nowrap">{{ \App\Support\Angka::sumbu($v) }}</span>
                        <div class="w-full rounded-t" style="height: {{ $persen }}%; background: {{ $w }}; min-height: {{ $v > 0 ? '2px' : '0' }};"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- label sumbu X --}}
    <div class="flex">
        <div class="w-5 shrink-0"></div>
        <div class="w-20 shrink-0"></div>
        <div class="flex-1 flex gap-3 px-3 mt-1.5">
            @foreach($rows as $row)
                <div class="flex-1 min-w-0 text-center text-[10px] text-gray-500 leading-tight break-normal">{{ $row['label'] }}</div>
            @endforeach
        </div>
    </div>

    @if($dipangkas)
        <p class="text-[10px] text-gray-400 mt-2">
            Grafik menampilkan {{ $maksBatang - 1 }} nilai teratas; {{ $sisa }} lainnya digabung menjadi "Lainnya". Rincian lengkap ada pada tabel di bawah.
        </p>
    @endif

    {{-- angka lengkap (tidak dibulatkan) --}}
    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-[11px]">
            <thead class="text-gray-400 uppercase text-[10px]">
                <tr>
                    <th class="text-left font-medium py-1 pr-3">Keterangan</th>
                    <th class="text-right font-medium py-1">Nilai ({{ $satuan }})</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($semua as $row)
                    <tr>
                        <td class="py-1 pr-3 text-gray-600">{{ $row['label'] }}</td>
                        <td class="py-1 text-right tabular-nums font-medium">{{ \App\Support\Angka::lengkap($row['value'], $desimal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
