{{--
  Kartu ringkasan angka LENGKAP (tidak dibulatkan).
  items: [['label'=>'Total Sumber Daya','nilai'=>123.45,'satuan'=>'ton','desimal'=>2,'catatan'=>'...'], ...]
--}}
@props(['items' => []])
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
    @foreach($items as $it)
        @php
            $satuan = $it['satuan'] ?? '';
            [$ringkas, $satuanRingkas] = \App\Support\Angka::format((float) ($it['nilai'] ?? 0), $satuan);
        @endphp
        <div class="bg-white border border-gray-200 rounded-md p-4">
            <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 mb-1.5">{{ $it['label'] }}</div>
            <div class="text-2xl font-semibold text-[#1B1F24] break-all leading-tight tabular-nums">{{ \App\Support\Angka::lengkap($it['nilai'] ?? 0, $it['desimal'] ?? 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $satuan }}@if($satuan === 'ton') &nbsp;·&nbsp; ≈ {{ $ringkas }} {{ $satuanRingkas }}@endif</div>
            @if(!empty($it['catatan']))
                <div class="text-[11px] text-gray-400 mt-1">{{ $it['catatan'] }}</div>
            @endif
        </div>
    @endforeach
</div>
