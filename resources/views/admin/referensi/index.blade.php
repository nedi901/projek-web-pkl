<x-layouts.admin title="Kelola Referensi">
    <h1 class="text-xl font-bold mb-1">Kelola Referensi</h1>
    <p class="text-xs text-gray-500 mb-5">Data acuan yang dipakai di formulir dan grafik semua domain.</p>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-2.5 mb-4">{{ session('error') }}</div>
    @endif

    @foreach($grup as $namaGrup => $items)
        <div class="mb-5">
            <div class="text-[10px] uppercase tracking-wide text-gray-400 mb-2">{{ $namaGrup }}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($items as $it)
                    <a href="{{ route('superuser.referensi.daftar', $it['slug']) }}"
                       class="bg-white border border-gray-200 hover:border-[#F2C230] rounded-xl px-4 py-3 flex items-center justify-between">
                        <span class="text-sm font-medium">{{ $it['label'] }}</span>
                        <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">{{ number_format($it['jumlah'], 0, ',', '.') }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</x-layouts.admin>
