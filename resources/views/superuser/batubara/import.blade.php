<x-layouts.superuser title="Import Data Batubara">
    <h1 class="text-xl font-bold mb-1">Import Data dari Excel</h1>
    <p class="text-xs text-gray-500 mb-4">Upload file sesuai format export Katalog KUGI</p>

    <div class="max-w-lg bg-white border rounded-xl p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-2.5 mb-4">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('superuser.batubara.import') }}" enctype="multipart/form-data">
            @csrf
            <label class="text-xs text-gray-500 block mb-1">File Excel (.xlsx)</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full border-gray-300 rounded-md text-sm mb-4">

            <div class="bg-gray-50 text-xs text-gray-500 rounded-md p-3 mb-4">
                Kolom wajib: TAHUN DATA, TAHUN NERACA, IDBB, NAMOBJ, KLSBB, STATDIKBB, TEREKA, TERTUNJUK, TERUKUR, TOTAL_SD, TERKIRA, TERBUKTI, TOTAL_CAD, PULAU, PROVINSI, KABUPATEN
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('superuser.batubara.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md">Batal</a>
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">Import</button>
            </div>
        </form>
    </div>
</x-layouts.superuser>