<?php

namespace App\Imports;

use App\Imports\Concerns\MelaporkanHasil;
use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KelompokKomoditasLogam;
use App\Models\KomoditasLogam;
use App\Models\NeracaMineralLogam;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use App\Support\SelAngka;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Kolom Excel (baris judul): provinsi, pulau, kabupaten, kellgm, jnskom, statdiklgm, idinstansi, idlgm, tahun_data,
 * tahun_neraca, namobj, bjhhip, loghip, bjhtrka, logtrka, bjhtjuk, logtjuk, bjhtkur, logtkur, bjhtkira, logtkira,
 * bjhtbkt, logtbkt, remark, bujur, lintang.
 * Total sumber daya dan cadangan dihitung otomatis dari kolom-kolom di atas.
 */
class MineralLogamImport implements SkipsEmptyRows, ToModel, WithHeadingRow
{
    use MelaporkanHasil;

    public function model(array $row): Model|array|null
    {
        if (blank($row['idlgm']) || blank($row['namobj']) || blank($row['provinsi']) || blank($row['kabupaten'])
            || blank($row['kellgm']) || blank($row['jnskom']) || blank($row['statdiklgm'])
            || blank($row['tahun_data']) || blank($row['tahun_neraca'])) {
            return $this->lewati('ada baris dengan isian wajib kosong (idlgm, namobj, provinsi, kabupaten, kellgm, jnskom, statdiklgm, tahun)');
        }

        if (NeracaMineralLogam::withTrashed()->where('idml', $row['idlgm'])->exists()) {
            return $this->lewati("idlgm {$row['idlgm']} sudah ada");
        }

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

        $idInstansi = ! blank($row['idinstansi'] ?? null)
            ? IdInstansi::firstOrCreate(['label' => trim($row['idinstansi'])])
            : null;

        $hipBijih = SelAngka::ke($row['bjhhip'] ?? null);
        $hipLogam = SelAngka::ke($row['loghip'] ?? null);
        $trkaBijih = SelAngka::ke($row['bjhtrka'] ?? null);
        $trkaLogam = SelAngka::ke($row['logtrka'] ?? null);
        $tjukBijih = SelAngka::ke($row['bjhtjuk'] ?? null);
        $tjukLogam = SelAngka::ke($row['logtjuk'] ?? null);
        $tkurBijih = SelAngka::ke($row['bjhtkur'] ?? null);
        $tkurLogam = SelAngka::ke($row['logtkur'] ?? null);
        $kiraBijih = SelAngka::ke($row['bjhtkira'] ?? null);
        $kiraLogam = SelAngka::ke($row['logtkira'] ?? null);
        $bktBijih = SelAngka::ke($row['bjhtbkt'] ?? null);
        $bktLogam = SelAngka::ke($row['logtbkt'] ?? null);

        return $this->terima(new NeracaMineralLogam([
            'idml' => (int) $row['idlgm'],
            'tahun_data' => $row['tahun_data'],
            'tahun_neraca' => $row['tahun_neraca'],
            'nama_objek' => trim($row['namobj']),
            'komoditas_logam_id' => $komoditas->id,
            'stat_dik_bb_id' => $statDikBb->id,
            'id_instansi_id' => $idInstansi?->id,
            'hipotetik_bijih' => $hipBijih,
            'hipotetik_logam' => $hipLogam,
            'tereka_bijih' => $trkaBijih,
            'tereka_logam' => $trkaLogam,
            'tertunjuk_bijih' => $tjukBijih,
            'tertunjuk_logam' => $tjukLogam,
            'terukur_bijih' => $tkurBijih,
            'terukur_logam' => $tkurLogam,
            'total_sd_bijih' => $trkaBijih + $tjukBijih + $tkurBijih,
            'total_sd_logam' => $trkaLogam + $tjukLogam + $tkurLogam,
            'terkira_bijih' => $kiraBijih,
            'terkira_logam' => $kiraLogam,
            'terbukti_bijih' => $bktBijih,
            'terbukti_logam' => $bktLogam,
            'total_cad_bijih' => $kiraBijih + $bktBijih,
            'total_cad_logam' => $kiraLogam + $bktLogam,
            'remark' => $row['remark'] ?? null,
            'provinsi_id' => $provinsi->id,
            'kabupaten_id' => $kabupaten->id,
            'bujur' => SelAngka::keAtauNull($row['bujur'] ?? null),
            'lintang' => SelAngka::keAtauNull($row['lintang'] ?? null),
        ]));
    }
}
