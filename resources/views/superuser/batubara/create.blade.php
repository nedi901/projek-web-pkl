<x-layouts.superuser title="Tambah Data Batubara">
    <h1 class="text-xl font-bold mb-1">Tambah Data Batubara</h1>
    <p class="text-xs text-gray-500 mb-4">Isi sesuai data terverifikasi</p>

    <div class="max-w-2xl bg-white border rounded-xl p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-2.5 mb-4">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('superuser.batubara.store') }}" class="space-y-4">
            @csrf

            <div class="text-xs uppercase text-orange-700 font-mono">Info Umum</div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">ID Batubara</label>
                    <input type="number" name="idbb" value="{{ old('idbb') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Nama Lokasi</label>
                    <input type="text" name="nama_objek" value="{{ old('nama_objek') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Provinsi</label>
                    <select name="provinsi_id" id="provinsi_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Provinsi —</option>
                        @foreach($provinsis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_provinsi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kabupaten</label>
                    <select name="kabupaten_id" id="kabupaten_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Provinsi Dulu —</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tahun Data</label>
                    <input type="number" name="tahun_data" value="{{ old('tahun_data', 2024) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tahun Neraca</label>
                    <input type="number" name="tahun_neraca" value="{{ old('tahun_neraca', 2024) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kelas Kalori</label>
                    <select name="kelas_kalori_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelasKaloris as $k)
                            <option value="{{ $k->id }}">{{ $k->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Status Eksplorasi</label>
                    <select name="stat_dik_bb_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Status —</option>
                        @foreach($statDikBbs as $s)
                            <option value="{{ $s->id }}">{{ $s->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Instansi</label>
                    <select name="id_instansi_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Instansi —</option>
                        @foreach($idInstansis as $i)
                            <option value="{{ $i->id }}">{{ $i->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-xs uppercase text-orange-700 font-mono pt-2">Sumber Daya (Juta Ton)</div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tereka</label>
                    <input type="number" step="0.001" name="tereka" value="{{ old('tereka', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tertunjuk</label>
                    <input type="number" step="0.001" name="tertunjuk" value="{{ old('tertunjuk', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terukur</label>
                    <input type="number" step="0.001" name="terukur" value="{{ old('terukur', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div class="text-xs uppercase text-orange-700 font-mono pt-2">Cadangan (Juta Ton)</div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terkira</label>
                    <input type="number" step="0.001" name="terkira" value="{{ old('terkira', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terbukti</label>
                    <input type="number" step="0.001" name="terbukti" value="{{ old('terbukti', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div>
                <label class="text-xs text-gray-500 block mb-1">Catatan (opsional)</label>
                <textarea name="remark" rows="2" class="w-full border-gray-300 rounded-md text-sm">{{ old('remark') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('superuser.batubara.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md">Batal</a>
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">Simpan Data</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('provinsi_id').addEventListener('change', function () {
            const provinsiId = this.value;
            const kabupatenSelect = document.getElementById('kabupaten_id');
            kabupatenSelect.innerHTML = '<option>Memuat...</option>';

            fetch(`/superuser/batubara/kabupaten/${provinsiId}`)
                .then(res => res.json())
                .then(data => {
                    kabupatenSelect.innerHTML = '<option value="">— Pilih Kabupaten —</option>';
                    data.forEach(k => {
                        kabupatenSelect.innerHTML += `<option value="${k.id}">${k.nama_kabupaten}</option>`;
                    });
                });
        });
    </script>
</x-layouts.superuser>