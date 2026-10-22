<?php

namespace App\Imports;

use App\Imports\Concerns\MelaporkanHasil;
use App\Models\Kabupaten;
use App\Models\NeracaGambut;
use App\Models\Provinsi;
use App\Support\SelAngka;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Kolom Excel (baris judul): idgb, nama_objek, tahun_data, tahun_neraca, provinsi, kabupaten, nilai_kalori_min,
 * nilai_kalori_max, luas_ha, volume_juta_m3, total_sd, bujur, lintang, remark.
 */
class GambutImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    use MelaporkanHasil;

    public function model(array $row): Model|array|null
    {
        if (blank($row['idgb']) || blank($row['nama_objek']) || blank($row['provinsi']) || blank($row['kabupaten'])
            || blank($row['tahun_data']) || blank($row['tahun_neraca'])) {
            return $this->lewati('ada baris dengan isian wajib kosong (idgb, nama_objek, provinsi, kabupaten, tahun)');
        }

        if (NeracaGambut::withTrashed()->where('idgb', $row['idgb'])->exists()) {
            return $this->lewati("idgb {$row['idgb']} sudah ada");
        }

        $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => trim($row['provinsi'])]);
        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        return $this->terima(new NeracaGambut([
            'idgb' => (int) $row['idgb'],
            'nama_objek' => trim($row['nama_objek']),
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'nilai_kalori_min' => SelAngka::keAtauNull($row['nilai_kalori_min'] ?? null),
            'nilai_kalori_max' => SelAngka::keAtauNull($row['nilai_kalori_max'] ?? null),
            'luas_ha' => SelAngka::ke($row['luas_ha'] ?? null),
            'volume_juta_m3' => SelAngka::ke($row['volume_juta_m3'] ?? null),
            'total_sd' => SelAngka::ke($row['total_sd'] ?? null),
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => SelAngka::keAtauNull($row['bujur'] ?? null),
            'lintang' => SelAngka::keAtauNull($row['lintang'] ?? null),
        ]));
    }
}
