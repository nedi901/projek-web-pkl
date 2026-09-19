<?php
namespace App\Imports;

use App\Models\NeracaMineralLogam;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KomoditasLogam;
use App\Models\KelompokKomoditasLogam;
use App\Models\StatDikBb;
use App\Models\IdInstansi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class MineralLogamImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $provinsi = Provinsi::firstOrCreate(
            ['nama_provinsi' => trim($row['provinsi'])],
            ['pulau' => trim($row['pulau'] ?? '')]
        );

        $kabupaten = Kabupaten::firstOrCreate([
            'provinsi_id' => $provinsi->id,
            'nama_kabupaten' => trim($row['kabupaten']),
        ]);

        $kelompok = KelompokKomoditasLogam::firstOrCreate(['nama_kelompok' => trim($row['kellgm'])]);

        $komoditas = KomoditasLogam::firstOrCreate([
            'kelompok_komoditas_logam_id' => $kelompok->id,
            'nama_komoditas' => trim($row['jnskom']),
        ]);

        $statDikBb = StatDikBb::firstOrCreate(['label' => trim($row['statdiklgm'])]);

        $idInstansi = !empty($row['idinstansi'])
            ? IdInstansi::firstOrCreate(['label' => trim($row['idinstansi'])])
            : null;

        if (NeracaMineralLogam::where('idml', $row['idlgm'])->exists()) {
            return null;
        }

        return new NeracaMineralLogam([
            'idml' => $row['idlgm'],
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'nama_objek' => trim($row['namobj']),
            'komoditas_logam_id' => $komoditas->id,
            'stat_dik_bb_id' => $statDikBb->id,
            'id_instansi_id' => $idInstansi?->id,
            'hipotetik_bijih' => $row['bjhhip'] ?? 0,
            'hipotetik_logam' => $row['loghip'] ?? 0,
            'tereka_bijih' => $row['bjhtrka'] ?? 0,
            'tereka_logam' => $row['logtrka'] ?? 0,
            'tertunjuk_bijih' => $row['bjhtjuk'] ?? 0,
            'tertunjuk_logam' => $row['logtjuk'] ?? 0,
            'terukur_bijih' => $row['bjhtkur'] ?? 0,
            'terukur_logam' => $row['logtkur'] ?? 0,
            'total_sd_bijih' => ($row['bjhtrka'] ?? 0) + ($row['bjhtjuk'] ?? 0) + ($row['bjhtkur'] ?? 0),
            'total_sd_logam' => ($row['logtrka'] ?? 0) + ($row['logtjuk'] ?? 0) + ($row['logtkur'] ?? 0),
            'terkira_bijih' => $row['bjhtkira'] ?? 0,
            'terkira_logam' => $row['logtkira'] ?? 0,
            'terbukti_bijih' => $row['bjhtbkt'] ?? 0,
            'terbukti_logam' => $row['logtbkt'] ?? 0,
            'total_cad_bijih' => ($row['bjhtkira'] ?? 0) + ($row['bjhtbkt'] ?? 0),
            'total_cad_logam' => ($row['logtkira'] ?? 0) + ($row['logtbkt'] ?? 0),
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => $row['bujur'] ?? null,
            'lintang' => $row['lintang'] ?? null,
        ]);
    }
}