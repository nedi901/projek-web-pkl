<?php

namespace App\Imports;

use App\Models\NeracaBatubara;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\StatDikBb;
use App\Models\IdInstansi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class BatubaraImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // Cari atau bikin data referensi berdasarkan nama (bukan ID) dari Excel
        $provinsi = Provinsi::firstOrCreate(
            ['nama_provinsi' => trim($row['provinsi'])],
            ['pulau' => trim($row['pulau'] ?? '')]
        );

        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        $kelasKalori = KelasKalori::firstOrCreate(
            ['label' => trim($row['klsbb'])],
            ['rentang' => '-']
        );

        $statDikBb = StatDikBb::firstOrCreate(['label' => trim($row['statdikbb'])]);

        $idInstansi = !empty($row['idinstansi'])
            ? IdInstansi::firstOrCreate(['label' => trim($row['idinstansi'])])
            : null;

        // Skip kalo idbb udah ada (hindari duplikat pas import ulang)
        if (NeracaBatubara::where('idbb', $row['idbb'])->exists()) {
            return null;
        }

        return new NeracaBatubara([
            'idbb' => $row['idbb'],
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'nama_objek' => trim($row['namobj']),
            'kelas_kalori_id' => $kelasKalori->id,
            'stat_dik_bb_id' => $statDikBb->id,
            'id_instansi_id' => $idInstansi?->id,
            'hipbb' => $row['hipbb'] ?? 0,
            'tereka' => $row['tereka'] ?? 0,
            'tertunjuk' => $row['tertunjuk'] ?? 0,
            'terukur' => $row['terukur'] ?? 0,
            'total_sd' => $row['total_sd'] ?? 0,
            'terkira' => $row['terkira'] ?? 0,
            'terbukti' => $row['terbukti'] ?? 0,
            'total_cad' => $row['total_cad'] ?? 0,
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => $row['bujur'] ?? null,
            'lintang' => $row['lintang'] ?? null,
        ]);
    }
}