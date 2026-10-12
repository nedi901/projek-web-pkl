<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use App\Models\Provinsi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Perapihan data referensi hasil audit. AMAN diulang (idempotent), jalankan sekali di database yang sudah ada:
 *   php artisan db:seed --class=RapikanDataSeeder
 *
 * 1. Provinsi tanpa nama ("") yang tersisa dari baris Felspar Sulawesi Tengah (nama terpotong pindah halaman di PDF buku)
 *    -> datanya dipindah ke provinsi "Sulawesi Tengah", provinsi kosong dihapus.
 * 2. Provinsi ganda "Sumatra Barat" (salah eja) digabung ke "Sumatera Barat".
 * 3. Kolom `pulau` diisi untuk semua provinsi (sebelumnya kebanyakan NULL, sehingga filter Pulau di Grafik Gambut
 *    cuma menampilkan sebagian provinsi).
 */
class RapikanDataSeeder extends Seeder
{
    /** tabel fakta yang punya provinsi_id & kabupaten_id */
    private const TABEL_NERACA = [
        'neraca_batubaras',
        'neraca_mineral_logams',
        'neraca_mineral_bukan_logams',
        'neraca_panas_bumis',
        'neraca_gambuts',
    ];

    private const PULAU = [
        'Sumatera' => ['Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi', 'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung'],
        'Jawa' => ['Banten', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Daerah Istimewa Yogyakarta', 'Jawa Timur'],
        'Bali' => ['Bali'],
        'Nusa Tenggara' => ['Nusa Tenggara Barat', 'Nusa Tenggara Timur'],
        'Kalimantan' => ['Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara'],
        'Sulawesi' => ['Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara'],
        'Maluku' => ['Maluku', 'Maluku Utara'],
        'Papua' => ['Papua', 'Papua Barat', 'Papua Barat Daya', 'Papua Tengah', 'Papua Selatan', 'Papua Pegunungan'],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $this->perbaikiProvinsiKosong();
            $this->gabungkanSumatraBarat();
            $this->isiPulau();
        });
    }

    private function perbaikiProvinsiKosong(): void
    {
        $kosong = Provinsi::whereRaw("TRIM(nama_provinsi) = ''")->get();
        if ($kosong->isEmpty()) {
            return;
        }

        $tengah = Provinsi::firstOrCreate(['nama_provinsi' => 'Sulawesi Tengah']);
        $kabTengah = Kabupaten::firstOrCreate(['provinsi_id' => $tengah->id, 'nama_kabupaten' => 'Rekap Provinsi']);

        foreach ($kosong as $p) {
            // satu-satunya data yang memakai provinsi kosong: Rekap Felspar (3 lokasi) -> Sulawesi Tengah
            DB::table('neraca_mineral_bukan_logams')
                ->where('provinsi_id', $p->id)
                ->where('nama_objek', 'like', 'Rekap Felspar%')
                ->update([
                    'provinsi_id' => $tengah->id,
                    'kabupaten_id' => $kabTengah->id,
                    'nama_objek' => 'Rekap Felspar Sulawesi Tengah (3 lokasi)',
                ]);

            $this->hapusJikaTidakDipakai($p);
        }
    }

    private function gabungkanSumatraBarat(): void
    {
        $dobel = Provinsi::where('nama_provinsi', 'Sumatra Barat')->first();
        if (! $dobel) {
            return;
        }

        $asli = Provinsi::where('nama_provinsi', 'Sumatera Barat')->first();
        if (! $asli) {
            $dobel->update(['nama_provinsi' => 'Sumatera Barat']);

            return;
        }

        foreach (Kabupaten::where('provinsi_id', $dobel->id)->get() as $kab) {
            $tujuan = Kabupaten::firstOrCreate(['provinsi_id' => $asli->id, 'nama_kabupaten' => $kab->nama_kabupaten]);
            foreach (self::TABEL_NERACA as $tabel) {
                DB::table($tabel)->where('kabupaten_id', $kab->id)->update(['kabupaten_id' => $tujuan->id]);
            }
            $kab->delete();
        }

        foreach (self::TABEL_NERACA as $tabel) {
            DB::table($tabel)->where('provinsi_id', $dobel->id)->update(['provinsi_id' => $asli->id]);
        }

        $dobel->delete();
    }

    private function isiPulau(): void
    {
        foreach (self::PULAU as $pulau => $daftar) {
            Provinsi::whereIn('nama_provinsi', $daftar)->update(['pulau' => $pulau]);
        }
    }

    /** hapus provinsi (dan kabupatennya) hanya kalau sudah tidak ada data yang memakainya */
    private function hapusJikaTidakDipakai(Provinsi $p): void
    {
        $dipakai = collect(self::TABEL_NERACA)->contains(
            fn ($t) => DB::table($t)->where('provinsi_id', $p->id)->exists()
        );

        if (! $dipakai) {
            Kabupaten::where('provinsi_id', $p->id)->delete();
            $p->delete();
        }
    }
}
