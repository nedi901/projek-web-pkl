<?php

namespace App\Imports;

use App\Imports\Concerns\MelaporkanHasil;
use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KomoditasBukanLogam;
use App\Models\NeracaMineralBukanLogam;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use App\Support\SelAngka;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Kolom Excel (baris judul): idbl, nama_objek, tahun_data, tahun_neraca, komoditas, provinsi, kabupaten, stat_dik,
 * instansi, hipotetik, tereka, tertunjuk, terukur, terkira, terbukti, bujur, lintang, remark.
 * "komoditas" dan "stat_dik" HARUS sudah ada di Kelola Referensi (nama persis sama); baris yang tidak dikenal
 * dilewati dan dilaporkan setelah import selesai.
 */
class MineralBukanLogamImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    use MelaporkanHasil;

    public function model(array $row): Model|array|null
    {
        if (blank($row['idbl']) || blank($row['nama_objek']) || blank($row['provinsi']) || blank($row['kabupaten'])
            || blank($row['komoditas']) || blank($row['stat_dik']) || blank($row['tahun_data']) || blank($row['tahun_neraca'])) {
            return $this->lewati('ada baris dengan isian wajib kosong (idbl, nama_objek, provinsi, kabupaten, komoditas, stat_dik, tahun)');
        }

        if (NeracaMineralBukanLogam::withTrashed()->where('idbl', $row['idbl'])->exists()) {
            return $this->lewati("idbl {$row['idbl']} sudah ada");
        }

        $komoditas = KomoditasBukanLogam::where('nama_komoditas', trim($row['komoditas']))->first();
        if (! $komoditas) {
            return $this->lewati('komoditas "' . trim($row['komoditas']) . '" tidak dikenal');
        }

        $statDik = StatDikBb::where('label', trim($row['stat_dik']))->first();
        if (! $statDik) {
            return $this->lewati('stat_dik "' . trim($row['stat_dik']) . '" tidak dikenal');
        }

        $instansi = ! blank($row['instansi'] ?? null)
            ? IdInstansi::where('label', trim($row['instansi']))->first()
            : null;

        $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => trim($row['provinsi'])]);
        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        $hipotetik = SelAngka::ke($row['hipotetik'] ?? null);
        $tereka = SelAngka::ke($row['tereka'] ?? null);
        $tertunjuk = SelAngka::ke($row['tertunjuk'] ?? null);
        $terukur = SelAngka::ke($row['terukur'] ?? null);
        $terkira = SelAngka::ke($row['terkira'] ?? null);
        $terbukti = SelAngka::ke($row['terbukti'] ?? null);

        return $this->terima(new NeracaMineralBukanLogam([
            'idbl' => (int) $row['idbl'],
            'nama_objek' => trim($row['nama_objek']),
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'komoditas_bukan_logam_id' => $komoditas->id,
            'stat_dik_bb_id' => $statDik->id,
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
            'bujur' => SelAngka::keAtauNull($row['bujur'] ?? null),
            'lintang' => SelAngka::keAtauNull($row['lintang'] ?? null),
        ]));
    }
}
