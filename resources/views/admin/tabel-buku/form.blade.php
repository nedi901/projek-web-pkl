<x-layouts.admin :title="$tabel ? 'Edit Tabel' : 'Tambah Tabel'">
    @php
        $aksi = $tabel ? route('superuser.tabel-buku.update', $tabel) : route('superuser.tabel-buku.store');
    @endphp

    <div class="max-w-4xl">
        <a href="{{ route('superuser.tabel-buku.index') }}" class="text-xs text-gray-400 hover:underline">← Kelola Tabel Buku</a>
        <h1 class="text-xl font-bold mb-4">{{ $tabel ? 'Edit' : 'Tambah' }} Tabel</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-4 py-2.5 mb-4">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ $aksi }}" class="bg-white border rounded-xl p-6 space-y-4">
            @csrf
            @if($tabel) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Domain (tampil di Grafik domain ini)</label>
                    <select name="domain" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        @foreach($domains as $slug => [$label])
                            <option value="{{ $slug }}" @selected(old('domain', $tabel?->domain) === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Tahun Data</label>
                    <input type="number" name="tahun_data" value="{{ old('tahun_data', $tabel?->tahun_data) }}" min="1990" max="2100"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-600 mb-1">Judul Tabel</label>
                    <input type="text" name="judul" value="{{ old('judul', $tabel?->judul) }}" required maxlength="255"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Sumber</label>
                    <input type="text" name="sumber" value="{{ old('sumber', $tabel?->sumber) }}" maxlength="255" placeholder="mis. Buku Neraca 2026, Tabel 3"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Urutan Tampil (angka kecil tampil lebih dulu)</label>
                    <input type="number" name="urutan" value="{{ old('urutan', $tabel?->urutan ?? 99) }}" min="0" max="999"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                @if($tabel)
                    <div class="sm:col-span-2 text-[11px] text-gray-400">Kode tabel: <span class="font-mono">{{ $tabel->kode }}</span></div>
                @endif
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Isi Tabel</label>
                <textarea name="data" rows="14" required spellcheck="false"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-xs font-mono whitespace-pre"
                          style="tab-size: 12; white-space: pre; overflow: auto;"
                          placeholder="Provinsi&#9;Sumber Daya&#9;Cadangan&#10;Kalimantan Timur&#9;1.234,56&#9;789,10&#10;TOTAL&#9;1.234,56&#9;789,10">{{ old('data', $teks) }}</textarea>
                <ul class="text-[11px] text-gray-500 mt-2 list-disc list-inside space-y-0.5">
                    <li>Tempel langsung dari Excel (kolom dipisahkan TAB). Baris pertama adalah judul kolom.</li>
                    <li>Kolom pertama berisi teks. Kolom lain otomatis menjadi angka jika seluruh isinya angka.</li>
                    <li>Angka memakai format Indonesia: titik untuk ribuan, koma untuk desimal (1.234,56). Isi "-" untuk nol.</li>
                    <li>Baris yang diawali TOTAL atau JUMLAH tampil tebal sebagai baris total.</li>
                </ul>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Catatan (tampil di bawah tabel)</label>
                <textarea name="catatan" rows="3" maxlength="3000" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">{{ old('catatan', $tabel?->catatan) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-5 py-2 rounded-md">Simpan</button>
                <a href="{{ route('superuser.tabel-buku.index') }}" class="text-sm text-gray-500 px-3 py-2">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
