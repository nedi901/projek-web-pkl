@props(['domain'])
@php
    $tabels = \App\Models\TabelReferensi::where('domain', $domain)->orderBy('urutan')->get();

    // 0 pada kolom "num" ditampilkan "-" (sesuai penulisan buku); kolom "int" (jumlah) tetap tampil 0
    $fmt = function ($v, $col) {
        $tipe = $col['t'] ?? 'text';
        if ($v === null || $v === '') return '-';
        if ($tipe === 'text') return $v;
        if ($tipe === 'num' && (float) $v == 0.0) return '-';
        return number_format((float) $v, $col['d'] ?? 0, ',', '.');
    };
@endphp

@if($tabels->isNotEmpty())
<div class="mt-8">
    <h2 class="text-base font-bold">Tabel Data</h2>
    <p class="text-xs text-gray-500 mb-4">
        Tabel rekapitulasi resmi dari Buku Neraca Sumber Daya dan Cadangan Mineral dan Batubara Indonesia
        (edisi 2025 &amp; 2026). Klik judul tabel untuk membuka/menutup.
    </p>

    @foreach($tabels as $t)
        <details class="bg-white border border-gray-200 rounded-xl mb-3 overflow-hidden" @if($loop->first) open @endif>
            <summary class="cursor-pointer select-none px-5 py-3 flex items-start justify-between gap-4 hover:bg-gray-50">
                <span class="text-sm font-semibold">{{ $t->judul }}</span>
                <span class="text-[10px] text-gray-400 whitespace-nowrap pt-0.5">Data {{ $t->tahun_data }} · {{ $t->sumber }}</span>
            </summary>

            <div class="overflow-x-auto border-t border-gray-100">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-50 text-[10px] uppercase text-gray-500">
                        <tr>
                            @foreach($t->kolom as $col)
                                <th class="px-3 py-2 {{ ($col['t'] ?? 'text') === 'text' ? 'text-left' : 'text-right' }} whitespace-nowrap">{{ $col['l'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($t->baris as $row)
                            @php
                                $gaya = $row['s'] ?? '';
                                $kelasBaris = match ($gaya) {
                                    'total' => 'bg-gray-100 font-bold',
                                    'sub' => 'bg-gray-50 font-semibold',
                                    default => '',
                                };
                            @endphp
                            <tr class="{{ $kelasBaris }}">
                                @foreach($t->kolom as $i => $col)
                                    @php $isText = ($col['t'] ?? 'text') === 'text'; @endphp
                                    <td class="px-3 py-1.5 {{ $isText ? 'text-left' : 'text-right tabular-nums' }} {{ $i === 0 && $gaya === 'child' ? 'pl-8 text-gray-600' : '' }} {{ $isText && $i > 0 ? 'text-gray-600' : '' }}">
                                        {{ $fmt($row['c'][$i] ?? null, $col) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($t->catatan)
                <div class="px-5 py-3 text-[11px] text-gray-500 border-t border-gray-100">{{ $t->catatan }}</div>
            @endif
        </details>
    @endforeach
</div>
@endif
