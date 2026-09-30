<?php

namespace App\Imports;

use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\StatDikPb;
use App\Models\KlasifikasiTemperatur;
use App\Models\IdInstansi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Database\Eloquent\Model;

/**
 * Kolom Excel yang diharapkan (heading row, otomatis di-snake_case oleh maatwebsite/excel):
 * idpb | nama_objek | tahun_data | tahun_neraca | provinsi | kabupaten | stat_dik |
 * instansi | klasifikasi_temperatur | temperatur_reservoir | spekulatif | hipotetik |
 * terduga | mungkin | terbukti | kapasitas_terpasang | bujur | lintang | remark
 *
 * PENTING: stat_dik di sini HARUS label dari tabel stat_dik_pbs (Survei Pendahuluan Awal,
 * Survei Pendahuluan, Survei Rinci, Eksplorasi/Siap Dikembangkan, Eksploitasi/Terpasang) -
 * BUKAN stat_dik_bbs yang dipakai Batubara/Mineral Logam/Bukan Logam. Jangan disatukan.
 *
 * Belum pernah dites pakai sample Excel asli - sama seperti MineralLogamImport.php dan
 * MineralBukanLogamImport.php, cek dulu hasil importnya sebelum dipakai produksi.
 */
class PanasBumiImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row): Model|array|null
    {
        if (NeracaPanasBumi::withTrashed()->where('idpb', $row['idpb'])->exists()) {
            return null;
        }

        $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => trim($row['provinsi'])]);
        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        $statDik = StatDikPb::where('label', trim($row['stat_dik']))->first();
        $instansi = isset($row['instansi']) && trim((string) $row['instansi']) !== ''
            ? IdInstansi::where('label', trim($row['instansi']))->first()
            : null;
        $klasifikasi = isset($row['klasifikasi_temperatur']) && trim((string) $row['klasifikasi_temperatur']) !== ''
            ? KlasifikasiTemperatur::where('label', trim($row['klasifikasi_temperatur']))->first()
            : null;

        $spekulatif = (float) ($row['spekulatif'] ?? 0);
        $hipotetik = (float) ($row['hipotetik'] ?? 0);
        $terduga = (float) ($row['terduga'] ?? 0);
        $mungkin = (float) ($row['mungkin'] ?? 0);
        $terbukti = (float) ($row['terbukti'] ?? 0);

        return new NeracaPanasBumi([
            'idpb' => $row['idpb'],
            'nama_objek' => $row['nama_objek'],
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'stat_dik_pb_id' => $statDik?->id,
            'id_instansi_id' => $instansi?->id,
            'klasifikasi_temperatur_id' => $klasifikasi?->id,
            'temperatur_reservoir' => $row['temperatur_reservoir'] ?? null,
            'spekulatif' => $spekulatif,
            'hipotetik' => $hipotetik,
            'total_sd' => $spekulatif + $hipotetik,
            'terduga' => $terduga,
            'mungkin' => $mungkin,
            'terbukti' => $terbukti,
            'total_cad' => $terduga + $mungkin + $terbukti,
            'kapasitas_terpasang' => $row['kapasitas_terpasang'] ?? 0,
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => $row['bujur'] ?? null,
            'lintang' => $row['lintang'] ?? null,
        ]);
    }
}
