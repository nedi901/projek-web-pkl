<x-layouts.admin title="Dashboard Super User">
    @php
        $labelTahun = $tahun ? (string) $tahun : '2024–2025';
        $badge = ['dihapus' => 'bg-yellow-100 text-yellow-800', 'dipulihkan' => 'bg-green-100 text-green-800', 'dihapus_permanen' => 'bg-red-100 text-red-800'];
    @endphp

    <div class="flex flex-wrap justify-between items-end gap-3 mb-6">
        <div>
            <h1 class="text-2xl">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan sumber daya dan cadangan seluruh domain, akun, dan aktivitas data · data {{ $labelTahun }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label class="text-xs text-gray-500">Tahun data</label>
            <select name="tahun" onchange="this.form.submit()" class="border border-gray-300 rounded-md text-sm px-2 py-1.5 bg-white">
                <option value="" @selected(! $tahun)>2024–2025</option>
                <option value="2024" @selected($tahun === 2024)>2024</option>
                <option value="2025" @selected($tahun === 2025)>2025</option>
            </select>
        </form>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-4 py-2.5 mb-4">{{ session('error') }}</div>
    @endif

    {{-- Ringkasan per domain: angka lengkap (tidak dibulatkan) + proporsi sumber daya vs cadangan di dalam tiap domain --}}
    <div class="flex items-center gap-4 text-[11px] text-gray-500 mb-2">
        <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-sm bg-[#F2C230] inline-block"></i>Sumber daya</span>
        <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-sm bg-[#4A6FE3] inline-block"></i>Cadangan</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5 gap-3 mb-6">
        @foreach($domains as $d)
            @php
                [$angka, $satuan] = \App\Support\Angka::format($d['total_sd'], $d['satuan']);
                $gabungan = $d['total_sd'] + ($d['total_cad'] ?? 0);
                $pSd = $gabungan > 0 ? round($d['total_sd'] / $gabungan * 100) : 0;
            @endphp
            <div class="bg-white border border-gray-200 rounded-md p-4 flex flex-col">
                <div class="flex items-start justify-between gap-2">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500">{{ $d['label'] }}</div>
                    @if($d['terhapus'] > 0)
                        <a href="{{ route($d['prefix'] . '.histori.index') }}" class="text-[10px] bg-yellow-100 text-yellow-800 rounded px-1.5 py-0.5 shrink-0">{{ $d['terhapus'] }} di Histori</a>
                    @endif
                </div>
                <div class="mt-2 text-xl font-semibold leading-tight break-all tabular-nums">{{ \App\Support\Angka::lengkap($d['total_sd']) }}</div>
                <div class="text-xs text-gray-500">{{ $d['satuan'] }} · sumber daya @if($d['satuan'] === 'ton') · ≈ {{ $angka }} {{ $satuan }}@endif</div>
                @if($gabungan > 0)
                    <div class="flex h-1.5 rounded-full overflow-hidden bg-gray-200 mt-3">
                        <div class="bg-[#F2C230]" style="width: {{ $pSd }}%"></div>
                        <div class="bg-[#4A6FE3]" style="width: {{ 100 - $pSd }}%"></div>
                    </div>
                @endif
                <div class="text-[11px] text-gray-500 mt-2 break-all tabular-nums">
                    @if($d['total_cad'] !== null)
                        Cadangan: {{ \App\Support\Angka::lengkap($d['total_cad']) }} {{ $d['satuan'] }}
                    @else
                        Belum ada data cadangan
                    @endif
                </div>
                <div class="text-[11px] text-gray-400 mt-0.5 mb-3">{{ number_format($d['jumlah'], 0, ',', '.') }} data · {{ $labelTahun }}</div>
                <div class="flex gap-2 text-xs mt-auto">
                    <a href="{{ route($d['prefix'] . '.grafik.index') }}" class="border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50">Grafik</a>
                    <a href="{{ route($d['prefix'] . '.index') }}" class="bg-[#F2C230] hover:bg-[#e0b526] font-semibold rounded-md px-3 py-1.5">Kelola Data</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
        {{-- Akun --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                <div class="text-sm font-semibold">Akun</div>
                <a href="{{ route('superuser.akun.index') }}" class="text-xs text-blue-600 hover:underline">Kelola Akun →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4 text-center">
                @foreach([['Total', $akun['total']], ['Super User', $akun['super_user']], ['Admin', $akun['admin']], ['User', $akun['user']]] as [$l, $n])
                    <div class="bg-gray-50 rounded-lg py-2">
                        <div class="text-lg font-bold">{{ $n }}</div>
                        <div class="text-[10px] text-gray-500">{{ $l }}</div>
                    </div>
                @endforeach
            </div>
            @if($akun['nonaktif'] > 0)
                <div class="text-[11px] text-gray-500 mb-3">{{ $akun['nonaktif'] }} akun nonaktif.</div>
            @endif

            <a href="{{ route('superuser.akun.create') }}" class="inline-block bg-[#F2C230] hover:bg-[#e0b526] text-black text-xs font-semibold px-4 py-2 rounded-md">+ Tambah Akun</a>
        </div>

        {{-- Aktivitas terbaru --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                <div class="text-sm font-semibold">Aktivitas Terbaru</div>
                <a href="{{ route('superuser.histori-semua.index', ['tab' => 'log']) }}" class="text-xs text-blue-600 hover:underline">Semua aktivitas →</a>
            </div>
            @forelse($aktivitas as $a)
                <div class="flex items-start justify-between gap-3 border-t border-gray-100 py-2 text-xs first:border-0">
                    <div class="min-w-0">
                        <div class="font-medium truncate">{{ $a->nama_data }}</div>
                        <div class="text-gray-500">
                            {{ \App\Http\Controllers\Admin\HistoriSemuaController::DOMAINS[$a->domain]['label'] ?? $a->domain }} · oleh {{ $a->user_nama ?? '-' }}
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $badge[$a->aksi] ?? 'bg-gray-100 text-gray-700' }}">{{ str_replace('_', ' ', $a->aksi) }}</span>
                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $a->created_at?->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M, H:i') }} WIB</div>
                    </div>
                </div>
            @empty
                <div class="text-xs text-gray-400">Belum ada aktivitas.</div>
            @endforelse
        </div>
    </div>

    {{-- Pintasan pengaturan --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('superuser.referensi.index') }}" class="bg-white border border-gray-200 hover:border-gray-400 rounded-md p-4 block">
            <div class="text-sm font-semibold">Kelola Referensi</div>
            <div class="text-xs text-gray-500 mt-1">Provinsi, kabupaten, komoditas, status penyelidikan, kelas kalori.</div>
        </a>
        <a href="{{ route('superuser.tabel-buku.index') }}" class="bg-white border border-gray-200 hover:border-gray-400 rounded-md p-4 block">
            <div class="text-sm font-semibold">Kelola Tabel Buku</div>
            <div class="text-xs text-gray-500 mt-1">{{ $jumlahTabelBuku }} tabel rekapitulasi tampil di Grafik dan Visualisasi.</div>
        </a>
        <a href="{{ route('superuser.histori-semua.index') }}" class="bg-white border border-gray-200 hover:border-gray-400 rounded-md p-4 block">
            <div class="text-sm font-semibold">Histori Semua Domain</div>
            <div class="text-xs text-gray-500 mt-1">{{ $totalTerhapus }} data terhapus dapat dipulihkan.</div>
        </a>
    </div>
</x-layouts.admin>
