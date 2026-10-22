<?php

namespace App\Imports;

use App\Imports\Concerns\MelaporkanHasil;
use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\NeracaBatubara;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use App\Support\SelAngka;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Kolom Excel (baris judul): provinsi, pulau, kabupaten, klsbb, statdikbb, idinstansi, idbb, tahun_data,
 * tahun_neraca, namobj, hipbb, tereka, tertunjuk, terukur, total_sd, terkira, terbukti, total_cad, remark, bujur, lintang.
 * Referensi (provinsi, kabupaten, kelas kalori, status, instansi) dicari berdasarkan NAMA dan dibuat kalau belum ada.
 */
class BatubaraImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    use MelaporkanHasil;

    public function model(array $row): Model|array|null
    {
        if (blank($row['idbb']) || blank($row['namobj']) || blank($row['provinsi']) || blank($row['kabupaten'])
            || blank($row['klsbb']) || blank($row['statdikbb']) || blank($row['tahun_data']) || blank($row['tahun_neraca'])) {
            return $this->lewati('ada baris dengan isian wajib kosong (idbb, namobj, provinsi, kabupaten, klsbb, statdikbb, tahun)');
        }

        // Skip kalau idbb sudah ada (hindari duplikat saat import ulang)
        if (NeracaBatubara::withTrashed()->where('idbb', $row['idbb'])->exists()) {
            return $this->lewati("idbb {$row['idbb']} sudah ada");
        }

        $provinsi = Provinsi::firstOrCreate(
            ['nama_provinsi' => trim($row['provinsi'])],
            ['pulau' => trim($row['pulau'] ?? '')]
        );

        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        $kelasKalori = KelasKalori::firstOrCreate(['label' => trim($row['klsbb'])], ['rentang' => '-']);
        $statDikBb = StatDikBb::firstOrCreate(['label' => trim($row['statdikbb'])]);

        $idInstansi = ! blank($row['idinstansi'] ?? null)
            ? IdInstansi::firstOrCreate(['label' => trim($row['idinstansi'])])
            : null;

        return $this->terima(new NeracaBatubara([
            'idbb' => (int) $row['idbb'],
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'nama_objek' => trim($row['namobj']),
            'kelas_kalori_id' => $kelasKalori->id,
            'stat_dik_bb_id' => $statDikBb->id,
            'id_instansi_id' => $idInstansi?->id,
            'hipbb' => SelAngka::ke($row['hipbb'] ?? null),
            'tereka' => SelAngka::ke($row['tereka'] ?? null),
            'tertunjuk' => SelAngka::ke($row['tertunjuk'] ?? null),
            'terukur' => SelAngka::ke($row['terukur'] ?? null),
            'total_sd' => SelAngka::ke($row['total_sd'] ?? null),
            'terkira' => SelAngka::ke($row['terkira'] ?? null),
            'terbukti' => SelAngka::ke($row['terbukti'] ?? null),
            'total_cad' => SelAngka::ke($row['total_cad'] ?? null),
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => SelAngka::keAtauNull($row['bujur'] ?? null),
            'lintang' => SelAngka::keAtauNull($row['lintang'] ?? null),
        ]));
    }
}
