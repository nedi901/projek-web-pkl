<?php

namespace App\Imports;

use App\Models\NeracaMineralBukanLogam;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KomoditasBukanLogam;
use App\Models\StatDikBb;
use App\Models\IdInstansi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Database\Eloquent\Model;

/**
 * Kolom Excel yang diharapkan (heading row, otomatis di-snake_case oleh maatwebsite/excel):
 * idbl | nama_objek | tahun_data | tahun_neraca | komoditas | provinsi | kabupaten |
 * stat_dik | instansi | hipotetik | tereka | tertunjuk | terukur | terkira | terbukti |
 * bujur | lintang | remark
 *
 * Sesuaikan nama kolom kalau template Excel KUGI asli kamu beda - tinggal ganti key
 * $row['...'] di bawah. Belum pernah dites pakai sample Excel asli (sama seperti
 * MineralLogamImport.php) - cek dulu hasil importnya sebelum dipakai produksi.
 */
class MineralBukanLogamImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row): Model|array|null
    {
        // skip kalau idbl sudah ada (hindari duplikat)
        if (NeracaMineralBukanLogam::where('idbl', $row['idbl'])->exists()) {
            return null;
        }

        $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => trim($row['provinsi'])]);
        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        $komoditas = KomoditasBukanLogam::where('nama_komoditas', trim($row['komoditas']))->first();
        if (! $komoditas) {
            return null; // komoditas gak dikenal - skip baris ini
        }

        $statDik = StatDikBb::where('label', trim($row['stat_dik']))->first();
        $instansi = isset($row['instansi']) && trim((string) $row['instansi']) !== ''
            ? IdInstansi::where('label', trim($row['instansi']))->first()
            : null;

        $hipotetik = (float) ($row['hipotetik'] ?? 0);
        $tereka = (float) ($row['tereka'] ?? 0);
        $tertunjuk = (float) ($row['tertunjuk'] ?? 0);
        $terukur = (float) ($row['terukur'] ?? 0);
        $terkira = (float) ($row['terkira'] ?? 0);
        $terbukti = (float) ($row['terbukti'] ?? 0);

        return new NeracaMineralBukanLogam([
            'idbl' => $row['idbl'],
            'nama_objek' => $row['nama_objek'],
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'komoditas_bukan_logam_id' => $komoditas->id,
            'stat_dik_bb_id' => $statDik?->id,
            'id_instansi_id' => $instansi?->id,
            'hipotetik' => $hipotetik,
            'tereka' => $tereka,
            'tertunjuk' => $tertunjuk,
            'terukur' => $terukur,
            'total_sd' => $tereka + $tertunjuk + $terukur,
            'terkira' => $terkira,
            'terbukti' => $terbukti,
            'total_cad' => $terkira + $terbukti,
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => $row['bujur'] ?? null,
            'lintang' => $row['lintang'] ?? null,
        ]);
    }
}
