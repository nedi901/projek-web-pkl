<x-layouts.user title="Beranda">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-xl font-bold">Beranda</h1>
            <p class="text-xs text-gray-500 mt-0.5">Informasi Neraca Sumber Daya Mineral, Batubara, dan Panas Bumi Indonesia</p>
        </div>
        <div>
            <label class="text-[10px] text-gray-400 block mb-1">Tahun</label>
            <select class="border border-gray-300 rounded-md text-sm px-2 py-1">
                <option>2024</option>
            </select>
        </div>
    </div>

    @php
        $sdMineral = $totalMineralLogam + $totalBukanLogam;
        $sdBatubara = $totalBatubara;
        $sdPanasBumi = $totalPanasBumi;
        $totalGab = max($sdMineral + $sdBatubara + $sdPanasBumi, 1);
        $pctMineral = round($sdMineral / $totalGab * 100);
        $pctBatubara = round($sdBatubara / $totalGab * 100);
        $pctPanasBumi = max(0, 100 - $pctMineral - $pctBatubara);
    @endphp

    <div class="grid grid-cols-4 gap-4 mb-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 mb-2">Sumber Daya<br>Mineral</div>
            <div class="text-xl font-bold">{{ number_format($sdMineral, 1) }}<span class="text-sm font-normal"> ton</span></div>
            <div class="text-[11px] text-green-600 mt-1">↑ 4.2% dari 2023</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 mb-2">Sumber Daya<br>Batubara</div>
            <div class="text-xl font-bold">{{ number_format($sdBatubara, 1) }}<span class="text-sm font-normal"> ton</span></div>
            <div class="text-[11px] text-green-600 mt-1">↑ 2.1% dari 2023</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 mb-2">Sumber Daya<br>Panas Bumi</div>
            <div class="text-xl font-bold">{{ number_format($sdPanasBumi, 1) }}<span class="text-sm font-normal"> MWe</span></div>
            <div class="text-[11px] text-green-600 mt-1">↑ 1.8% dari 2023</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 mb-2">Lokasi<br>Terverifikasi</div>
            <div class="text-xl font-bold">{{ $jumlahLokasi }}</div>
            <div class="text-[11px] text-gray-400 mt-1">dari {{ $jumlahLokasi }} titik</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Trend Sumber Daya 2020-2024</div>
            <div class="flex items-end gap-3 h-40">
                <div class="flex-1 bg-[#F2C230] rounded-t" style="height:55%"></div>
                <div class="flex-1 bg-[#F2C230] rounded-t" style="height:65%"></div>
                <div class="flex-1 bg-[#F2C230] rounded-t" style="height:75%"></div>
                <div class="flex-1 bg-[#F2C230] rounded-t" style="height:85%"></div>
                <div class="flex-1 bg-[#4A6FE3] rounded-t" style="height:100%"></div>
            </div>
            <div class="flex gap-3 mt-2 text-[10px] text-gray-400">
                <div class="flex-1 text-center">2020</div>
                <div class="flex-1 text-center">2021</div>
                <div class="flex-1 text-center">2022</div>
                <div class="flex-1 text-center">2023</div>
                <div class="flex-1 text-center">2024</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="text-sm font-semibold mb-4">Proporsi per Domain</div>
            <div class="flex items-center justify-center">
                <div class="w-28 h-28 rounded-full"
                     style="background: conic-gradient(#F2C230 0% {{ $pctMineral }}%, #E08C2B {{ $pctMineral }}% {{ $pctMineral + $pctPanasBumi }}%, #B0B0B0 {{ $pctMineral + $pctPanasBumi }}% 100%);">
                    <div class="w-14 h-14 bg-white rounded-full m-7"></div>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-3 text-[11px]">
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#F2C230]"></span>Mineral {{ $pctMineral }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#E08C2B]"></span>Panas Bumi {{ $pctPanasBumi }}%</div>
                <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-[#B0B0B0]"></span>Batubara {{ $pctBatubara }}%</div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 flex justify-between items-center">
        <div>
            <div class="text-sm font-semibold">Lihat Data Lengkap</div>
            <div class="text-xs text-gray-500 mt-0.5">Telusuri seluruh neraca per provinsi dan komoditas</div>
        </div>
        <a href="{{ route('user.batubara.index') }}" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-xs font-semibold px-4 py-2 rounded-md">
            Buka Data →
        </a>
    </div>
</x-layouts.user>