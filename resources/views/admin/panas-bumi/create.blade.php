<x-layouts.admin title="Tambah Data Panas Bumi">
    <h1 class="text-xl font-bold mb-1">Tambah Data Panas Bumi</h1>
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

        <form method="POST" action="{{ route('admin.panas-bumi.store') }}" class="space-y-4">
            @csrf

            <div class="text-xs uppercase text-orange-700 font-mono">Info Umum</div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">ID Panas Bumi</label>
                    <input type="number" name="idpb" value="{{ old('idpb') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Nama Lokasi (WKP)</label>
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
                    <label class="text-xs text-gray-500 block mb-1">Status Penyelidikan</label>
                    <select name="stat_dik_pb_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Status —</option>
                        @foreach($statDikPbs as $s)
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
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Klasifikasi Temperatur</label>
                    <select name="klasifikasi_temperatur_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Klasifikasi —</option>
                        @foreach($klasifikasiTemperaturs as $k)
                            <option value="{{ $k->id }}">{{ $k->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Temperatur Reservoir (°C)</label>
                    <input type="number" step="0.01" name="temperatur_reservoir" value="{{ old('temperatur_reservoir') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Bujur (opsional)</label>
                    <input type="number" step="0.000001" name="bujur" value="{{ old('bujur') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Lintang (opsional)</label>
                    <input type="number" step="0.000001" name="lintang" value="{{ old('lintang') }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div class="text-xs uppercase text-orange-700 font-mono pt-2">Sumber Daya (MWe)</div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Spekulatif</label>
                    <input type="number" step="0.001" name="spekulatif" value="{{ old('spekulatif', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Hipotetik</label>
                    <input type="number" step="0.001" name="hipotetik" value="{{ old('hipotetik', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div class="text-xs uppercase text-orange-700 font-mono pt-2">Cadangan (MWe)</div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terduga</label>
                    <input type="number" step="0.001" name="terduga" value="{{ old('terduga', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Mungkin</label>
                    <input type="number" step="0.001" name="mungkin" value="{{ old('mungkin', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terbukti</label>
                    <input type="number" step="0.001" name="terbukti" value="{{ old('terbukti', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div class="text-xs uppercase text-orange-700 font-mono pt-2">Lainnya</div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">Kapasitas Terpasang (MW)</label>
                <input type="number" step="0.001" name="kapasitas_terpasang" value="{{ old('kapasitas_terpasang', 0) }}" class="w-full border-gray-300 rounded-md text-sm">
            </div>

            <div>
                <label class="text-xs text-gray-500 block mb-1">Catatan (opsional)</label>
                <textarea name="remark" rows="2" class="w-full border-gray-300 rounded-md text-sm">{{ old('remark') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('admin.panas-bumi.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md">Batal</a>
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">Simpan Data</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('provinsi_id').addEventListener('change', function () {
            const provinsiId = this.value;
            const kabupatenSelect = document.getElementById('kabupaten_id');
            kabupatenSelect.innerHTML = '<option>Memuat...</option>';

            fetch(`/admin/panas-bumi/kabupaten/${provinsiId}`)
                .then(res => res.json())
                .then(data => {
                    kabupatenSelect.innerHTML = '<option value="">— Pilih Kabupaten —</option>';
                    data.forEach(k => {
                        kabupatenSelect.innerHTML += `<option value="${k.id}">${k.nama_kabupaten}</option>`;
                    });
                });
        });
    </script>
</x-layouts.admin>
