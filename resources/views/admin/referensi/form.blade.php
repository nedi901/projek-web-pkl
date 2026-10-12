<x-layouts.admin :title="($row ? 'Edit ' : 'Tambah ') . $cfg['label']">
    @php
        $slug = $cfg['slug'];
        $aksi = $row ? route('superuser.referensi.update', [$slug, $row->id]) : route('superuser.referensi.store', $slug);
    @endphp

    <div class="max-w-xl">
        <a href="{{ route('superuser.referensi.daftar', $slug) }}" class="text-xs text-gray-400 hover:underline">← {{ $cfg['label'] }}</a>
        <h1 class="text-xl font-bold mb-4">{{ $row ? 'Edit' : 'Tambah' }} {{ $cfg['label'] }}</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-4 py-2.5 mb-4">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ $aksi }}" class="bg-white border rounded-xl p-6 space-y-4">
            @csrf
            @if($row) @method('PUT') @endif

            @foreach($cfg['kolom'] as $k)
                <div>
                    <label class="block text-xs text-gray-600 mb-1">{{ $k['l'] }}</label>
                    @if($k['tipe'] === 'relasi')
                        <select name="{{ $k['k'] }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">— pilih —</option>
                            @foreach($relasiOpsi[$k['k']] as $opsi)
                                <option value="{{ $opsi->id }}" @selected(old($k['k'], $row?->{$k['k']}) == $opsi->id)>{{ $opsi->{$k['tampil']} }}</option>
                            @endforeach
                        </select>
                    @elseif($k['tipe'] === 'pilihan')
                        <select name="{{ $k['k'] }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">— tidak diisi —</option>
                            @foreach($k['pilihan'] as $p)
                                <option value="{{ $p }}" @selected(old($k['k'], $row?->{$k['k']}) === $p)>{{ $p }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="{{ $k['k'] }}" value="{{ old($k['k'], $row?->{$k['k']}) }}" maxlength="{{ $k['max'] ?? 150 }}"
                               @if($k['wajib'] ?? true) required @endif
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                    @endif
                </div>
            @endforeach

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-5 py-2 rounded-md">Simpan</button>
                <a href="{{ route('superuser.referensi.daftar', $slug) }}" class="text-sm text-gray-500 px-3 py-2">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
