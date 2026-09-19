<x-layouts.superuser title="Edit Data Mineral Logam">
    <h1 class="text-xl font-bold mb-1">Edit Data Mineral Logam</h1>
    <p class="text-xs text-gray-500 mb-4">{{ $mineralLogam->nama_objek }} — ubah field yang perlu</p>

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

        <form method="POST" action="{{ route('superuser.mineral-logam.update', $mineralLogam) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="text-xs uppercase text-orange-700 font-mono">Info Umum</div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">ID Mineral Logam</label>
                    <input type="number" name="idml" value="{{ old('idml', $mineralLogam->idml) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Nama Lokasi</label>
                    <input type="text" name="nama_objek" value="{{ old('nama_objek', $mineralLogam->nama_objek) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Komoditas</label>
                    <select name="komoditas_logam_id" class="w-full border-gray-300 rounded-md text-sm">
                        @foreach($komoditasLogams as $k)
                            <option value="{{ $k->id }}" @selected($mineralLogam->komoditas_logam_id == $k->id)>{{ $k->nama_komoditas }} ({{ $k->kelompokKomoditasLogam->nama_kelompok }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Provinsi</label>
                    <select name="provinsi_id" id="provinsi_id" class="w-full border-gray-300 rounded-md text-sm">
                        @foreach($provinsis as $p)
                            <option value="{{ $p->id }}" @selected($mineralLogam->provinsi_id == $p->id)>{{ $p->nama_provinsi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Kabupaten</label>
                    <select name="kabupaten_id" id="kabupaten_id" class="w-full border-gray-300 rounded-md text-sm">
                        @foreach($kabupatens as $k)
                            <option value="{{ $k->id }}" @selected($mineralLogam->kabupaten_id == $k->id)>{{ $k->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tahun Data</label>
                    <input type="number" name="tahun_data" value="{{ old('tahun_data', $mineralLogam->tahun_data) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tahun Neraca</label>
                    <input type="number" name="tahun_neraca" value="{{ old('tahun_neraca', $mineralLogam->tahun_neraca) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Status Eksplorasi</label>
                    <select name="stat_dik_bb_id" class="w-full border-gray-300 rounded-md text-sm">
                        @foreach($statDikBbs as $s)
                            <option value="{{ $s->id }}" @selected($mineralLogam->stat_dik_bb_id == $s->id)>{{ $s->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Instansi</label>
                    <select name="id_instansi_id" class="w-full border-gray-300 rounded-md text-sm">
                        <option value="">— Pilih Instansi —</option>
                        @foreach($idInstansis as $i)
                            <option value="{{ $i->id }}" @selected($mineralLogam->id_instansi_id == $i->id)>{{ $i->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-xs uppercase text-orange-700 font-mono pt-2">Sumber Daya — Bijih / Logam (Ton)</div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Hipotetik (bijih)</label>
                    <input type="number" step="0.001" name="hipotetik_bijih" value="{{ old('hipotetik_bijih', $mineralLogam->hipotetik_bijih) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Hipotetik (logam)</label>
                    <input type="number" step="0.001" name="hipotetik_logam" value="{{ old('hipotetik_logam', $mineralLogam->hipotetik_logam) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tereka (bijih)</label>
                    <input type="number" step="0.001" name="tereka_bijih" value="{{ old('tereka_bijih', $mineralLogam->tereka_bijih) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tereka (logam)</label>
                    <input type="number" step="0.001" name="tereka_logam" value="{{ old('tereka_logam', $mineralLogam->tereka_logam) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tertunjuk (bijih)</label>
                    <input type="number" step="0.001" name="tertunjuk_bijih" value="{{ old('tertunjuk_bijih', $mineralLogam->tertunjuk_bijih) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Tertunjuk (logam)</label>
                    <input type="number" step="0.001" name="tertunjuk_logam" value="{{ old('tertunjuk_logam', $mineralLogam->tertunjuk_logam) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terukur (bijih)</label>
                    <input type="number" step="0.001" name="terukur_bijih" value="{{ old('terukur_bijih', $mineralLogam->terukur_bijih) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terukur (logam)</label>
                    <input type="number" step="0.001" name="terukur_logam" value="{{ old('terukur_logam', $mineralLogam->terukur_logam) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div class="text-xs uppercase text-orange-700 font-mono pt-2">Cadangan — Bijih / Logam (Ton)</div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terkira (bijih)</label>
                    <input type="number" step="0.001" name="terkira_bijih" value="{{ old('terkira_bijih', $mineralLogam->terkira_bijih) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terkira (logam)</label>
                    <input type="number" step="0.001" name="terkira_logam" value="{{ old('terkira_logam', $mineralLogam->terkira_logam) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terbukti (bijih)</label>
                    <input type="number" step="0.001" name="terbukti_bijih" value="{{ old('terbukti_bijih', $mineralLogam->terbukti_bijih) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Terbukti (logam)</label>
                    <input type="number" step="0.001" name="terbukti_logam" value="{{ old('terbukti_logam', $mineralLogam->terbukti_logam) }}" class="w-full border-gray-300 rounded-md text-sm">
                </div>
            </div>

            <div>
                <label class="text-xs text-gray-500 block mb-1">Catatan (opsional)</label>
                <textarea name="remark" rows="2" class="w-full border-gray-300 rounded-md text-sm">{{ old('remark', $mineralLogam->remark) }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('superuser.mineral-logam.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md">Batal</a>
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('provinsi_id').addEventListener('change', function () {
            const provinsiId = this.value;
            const kabupatenSelect = document.getElementById('kabupaten_id');
            kabupatenSelect.innerHTML = '<option>Memuat...</option>';

            fetch(`/superuser/mineral-logam/kabupaten/${provinsiId}`)
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