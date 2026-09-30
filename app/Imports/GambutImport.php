<?php

namespace App\Imports;

use App\Models\NeracaGambut;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Database\Eloquent\Model;

/**
 * Kolom Excel yang diharapkan (heading row):
 * idgb | nama_objek | tahun_data | tahun_neraca | provinsi | kabupaten |
 * nilai_kalori_min | nilai_kalori_max | luas_ha | volume_juta_m3 | total_sd |
 * bujur | lintang | remark
 *
 * Belum pernah dites pakai sample Excel asli - sama seperti domain lain.
 */
class GambutImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row): Model|array|null
    {
        if (NeracaGambut::withTrashed()->where('idgb', $row['idgb'])->exists()) {
            return null;
        }

        $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => trim($row['provinsi'])]);
        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        return new NeracaGambut([
            'idgb' => $row['idgb'],
            'nama_objek' => $row['nama_objek'],
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'nilai_kalori_min' => $row['nilai_kalori_min'] ?? null,
            'nilai_kalori_max' => $row['nilai_kalori_max'] ?? null,
            'luas_ha' => $row['luas_ha'] ?? 0,
            'volume_juta_m3' => $row['volume_juta_m3'] ?? 0,
            'total_sd' => $row['total_sd'] ?? 0,
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => $row['bujur'] ?? null,
            'lintang' => $row['lintang'] ?? null,
        ]);
    }
}
