<x-layouts.admin title="Import Data Mineral Logam">
    <h1 class="text-xl font-bold mb-1">Import Excel — Mineral Logam</h1>
    <p class="text-xs text-gray-500 mb-4">Upload file Excel sesuai template kolom yang ditentukan</p>

    <div class="max-w-lg bg-white border rounded-xl p-6">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-2.5 mb-4">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.mineral-logam.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs text-gray-500 block mb-1">File Excel (.xlsx / .xls)</label>
                <input type="file" name="file" accept=".xlsx,.xls" class="w-full border-gray-300 rounded-md text-sm">
            </div>
            <p class="text-xs text-gray-400">
                Kolom yang dibutuhkan tergantung isi MineralLogamImport.php kamu - cek lagi file itu,
                soalnya belum pernah dites pakai file Excel asli.
            </p>
            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('admin.mineral-logam.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md">Batal</a>
                <button type="submit" class="bg-[#F2C230] hover:bg-[#e0b526] text-black text-sm font-semibold px-4 py-2 rounded-md">Import</button>
            </div>
        </form>
    </div>
</x-layouts.admin>