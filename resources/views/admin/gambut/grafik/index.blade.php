<x-layouts.admin title="Grafik dan Visualisasi">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-xl font-bold">Grafik dan Visualisasi</h1>
            <p class="text-xs text-gray-500">
                @if($pulau)
                    Pulau {{ $pulau }} — Sumber Daya Gambut
                @else
                    Perbandingan Antar Provinsi — Gambut
                @endif
            </p>
        </div>
        <form method="GET">
            <select name="pulau" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm px-3 py-2">
                <option value="">Semua Pulau</option>
                @foreach($pulauList as $p)
                    <option value="{{ $p }}" @selected($pulau == $p)>{{ $p }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-xs uppercase text-gray-400 mb-1">Total Sumber Daya</div>
            <div class="text-2xl font-bold text-[#1a1a1a]">{{ number_format($totalSd, 2) }} <span class="text-sm font-normal text-gray-400">juta ton</span></div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-xs uppercase text-gray-400 mb-1">Total Luas</div>
            <div class="text-2xl font-bold text-[#1a1a1a]">{{ number_format($totalLuas, 2) }} <span class="text-sm font-normal text-gray-400">ha</span></div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-xs uppercase text-gray-400 mb-1">Total Volume</div>
            <div class="text-2xl font-bold text-[#1a1a1a]">{{ number_format($totalVolume, 2) }} <span class="text-sm font-normal text-gray-400">juta m³</span></div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="text-sm font-semibold mb-4">
            @if($pulau)
                Total Sumber Daya per Provinsi — Pulau {{ $pulau }} (Juta Ton)
            @else
                Total Sumber Daya per Provinsi (Juta Ton)
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
                    <div class="flex-1 text-center">{{ $row->provinsi->nama_provinsi ?? '-' }}</div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 text-sm py-10">Belum ada data</div>
        @endif
    </div>
</x-layouts.admin>
