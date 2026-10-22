<?php

namespace App\Imports;

use App\Imports\Concerns\MelaporkanHasil;
use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KlasifikasiTemperatur;
use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use App\Models\StatDikPb;
use App\Support\SelAngka;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Kolom Excel (baris judul): idpb, nama_objek, tahun_data, tahun_neraca, provinsi, kabupaten, stat_dik, instansi,
 * klasifikasi_temperatur, temperatur_reservoir, spekulatif, hipotetik, terduga, mungkin, terbukti,
 * kapasitas_terpasang, bujur, lintang, remark.
 *
 * PENTING: stat_dik HARUS label dari Status Penyelidikan Panas Bumi (tabel stat_dik_pbs), BUKAN stat_dik_bbs yang
 * dipakai Batubara/Mineral. Baris dengan stat_dik tidak dikenal dilewati dan dilaporkan setelah import selesai.
 */
class PanasBumiImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    use MelaporkanHasil;

    public function model(array $row): Model|array|null
    {
        if (blank($row['idpb']) || blank($row['nama_objek']) || blank($row['provinsi']) || blank($row['kabupaten'])
            || blank($row['stat_dik']) || blank($row['tahun_data']) || blank($row['tahun_neraca'])) {
            return $this->lewati('ada baris dengan isian wajib kosong (idpb, nama_objek, provinsi, kabupaten, stat_dik, tahun)');
        }

        if (NeracaPanasBumi::withTrashed()->where('idpb', $row['idpb'])->exists()) {
            return $this->lewati("idpb {$row['idpb']} sudah ada");
        }

        $statDik = StatDikPb::where('label', trim($row['stat_dik']))->first();
        if (! $statDik) {
            return $this->lewati('stat_dik "' . trim($row['stat_dik']) . '" tidak dikenal');
        }

        $instansi = ! blank($row['instansi'] ?? null)
            ? IdInstansi::where('label', trim($row['instansi']))->first()
            : null;

        $klasifikasi = ! blank($row['klasifikasi_temperatur'] ?? null)
            ? KlasifikasiTemperatur::where('label', trim($row['klasifikasi_temperatur']))->first()
            : null;

        $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => trim($row['provinsi'])]);
        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        $spekulatif = SelAngka::ke($row['spekulatif'] ?? null);
        $hipotetik = SelAngka::ke($row['hipotetik'] ?? null);
        $terduga = SelAngka::ke($row['terduga'] ?? null);
        $mungkin = SelAngka::ke($row['mungkin'] ?? null);
        $terbukti = SelAngka::ke($row['terbukti'] ?? null);

        return $this->terima(new NeracaPanasBumi([
            'idpb' => (int) $row['idpb'],
            'nama_objek' => trim($row['nama_objek']),
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'stat_dik_pb_id' => $statDik->id,
            'id_instansi_id' => $instansi?->id,
            'klasifikasi_temperatur_id' => $klasifikasi?->id,
            'temperatur_reservoir' => SelAngka::keAtauNull($row['temperatur_reservoir'] ?? null),
            'spekulatif' => $spekulatif,
            'hipotetik' => $hipotetik,
            'total_sd' => $spekulatif + $hipotetik,
            'terduga' => $terduga,
            'mungkin' => $mungkin,
            'terbukti' => $terbukti,
            'total_cad' => $terduga + $mungkin + $terbukti,
            'kapasitas_terpasang' => SelAngka::ke($row['kapasitas_terpasang'] ?? null),
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => SelAngka::keAtauNull($row['bujur'] ?? null),
            'lintang' => SelAngka::keAtauNull($row['lintang'] ?? null),
        ]));
    }
}
