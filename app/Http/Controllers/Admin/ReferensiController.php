<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\KelompokKomoditasBukanLogam;
use App\Models\KelompokKomoditasLogam;
use App\Models\KlasifikasiTemperatur;
use App\Models\KomoditasBukanLogam;
use App\Models\KomoditasLogam;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use App\Models\StatDikPb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Kelola Referensi (Super User): CRUD generik untuk semua tabel referensi, digerakkan oleh konfigurasi JENIS di bawah.
 * Hapus diblokir kalau data masih dipakai (dihitung langsung di database, termasuk data yang ada di Histori).
 */
class ReferensiController extends Controller
{
    public const PULAU = ['Sumatera', 'Jawa', 'Bali', 'Nusa Tenggara', 'Kalimantan', 'Sulawesi', 'Maluku', 'Papua'];

    /** tabel fakta + kolom FK, dipakai untuk pengecekan "masih dipakai" */
    private const NERACA = [
        'batubara' => ['neraca_batubaras', 'Data Batubara'],
        'logam' => ['neraca_mineral_logams', 'Data Mineral Logam'],
        'bukan_logam' => ['neraca_mineral_bukan_logams', 'Data Mineral Bukan Logam'],
        'panas_bumi' => ['neraca_panas_bumis', 'Data Panas Bumi'],
        'gambut' => ['neraca_gambuts', 'Data Gambut'],
    ];

    /** @return array<string, array> */
    private function jenis(): array
    {
        $teks = fn (string $k, string $l, array $x = []) => $x + ['k' => $k, 'l' => $l, 'tipe' => 'text', 'wajib' => true, 'max' => 150];

        $pakaiNeraca = fn (string $kolom, array $domain) => array_map(
            fn ($d) => [self::NERACA[$d][0], $kolom, self::NERACA[$d][1]],
            $domain
        );

        return [
            'provinsi' => [
                'grup' => 'Wilayah', 'model' => Provinsi::class, 'label' => 'Provinsi', 'urut' => 'nama_provinsi',
                'kolom' => [
                    $teks('nama_provinsi', 'Nama Provinsi', ['unik' => true]),
                    ['k' => 'pulau', 'l' => 'Pulau', 'tipe' => 'pilihan', 'pilihan' => self::PULAU, 'wajib' => false],
                ],
                'dipakai' => array_merge(
                    [['kabupatens', 'provinsi_id', 'Kabupaten']],
                    $pakaiNeraca('provinsi_id', array_keys(self::NERACA))
                ),
            ],
            'kabupaten' => [
                'grup' => 'Wilayah', 'model' => Kabupaten::class, 'label' => 'Kabupaten / Kota', 'urut' => 'nama_kabupaten',
                'kolom' => [
                    ['k' => 'provinsi_id', 'l' => 'Provinsi', 'tipe' => 'relasi', 'model' => Provinsi::class, 'tampil' => 'nama_provinsi', 'rel' => 'provinsi', 'wajib' => true],
                    $teks('nama_kabupaten', 'Nama Kabupaten / Kota', ['unik_dalam' => 'provinsi_id']),
                ],
                'dipakai' => $pakaiNeraca('kabupaten_id', array_keys(self::NERACA)),
            ],
            'kelompok-logam' => [
                'grup' => 'Mineral Logam', 'model' => KelompokKomoditasLogam::class, 'label' => 'Kelompok Komoditas Logam', 'urut' => 'nama_kelompok',
                'kolom' => [$teks('nama_kelompok', 'Nama Kelompok', ['unik' => true])],
                'dipakai' => [['komoditas_logams', 'kelompok_komoditas_logam_id', 'Komoditas Logam']],
            ],
            'komoditas-logam' => [
                'grup' => 'Mineral Logam', 'model' => KomoditasLogam::class, 'label' => 'Komoditas Logam', 'urut' => 'nama_komoditas',
                'kolom' => [
                    ['k' => 'kelompok_komoditas_logam_id', 'l' => 'Kelompok', 'tipe' => 'relasi', 'model' => KelompokKomoditasLogam::class, 'tampil' => 'nama_kelompok', 'rel' => null, 'wajib' => true],
                    $teks('nama_komoditas', 'Nama Komoditas', ['unik' => true]),
                ],
                'dipakai' => $pakaiNeraca('komoditas_logam_id', ['logam']),
            ],
            'kelompok-bukan-logam' => [
                'grup' => 'Mineral Bukan Logam', 'model' => KelompokKomoditasBukanLogam::class, 'label' => 'Kelompok Komoditas Bukan Logam', 'urut' => 'nama_kelompok',
                'kolom' => [$teks('nama_kelompok', 'Nama Kelompok', ['unik' => true])],
                'dipakai' => [['komoditas_bukan_logams', 'kelompok_komoditas_bukan_logam_id', 'Komoditas Bukan Logam']],
            ],
            'komoditas-bukan-logam' => [
                'grup' => 'Mineral Bukan Logam', 'model' => KomoditasBukanLogam::class, 'label' => 'Komoditas Bukan Logam', 'urut' => 'nama_komoditas',
                'kolom' => [
                    ['k' => 'kelompok_komoditas_bukan_logam_id', 'l' => 'Kelompok', 'tipe' => 'relasi', 'model' => KelompokKomoditasBukanLogam::class, 'tampil' => 'nama_kelompok', 'rel' => null, 'wajib' => true],
                    $teks('nama_komoditas', 'Nama Komoditas', ['unik' => true]),
                ],
                'dipakai' => $pakaiNeraca('komoditas_bukan_logam_id', ['bukan_logam']),
            ],
            'kelas-kalori' => [
                'grup' => 'Batubara', 'model' => KelasKalori::class, 'label' => 'Kelas Kalori', 'urut' => 'label',
                'kolom' => [
                    $teks('label', 'Label', ['unik' => true]),
                    $teks('rentang', 'Rentang Kalori', ['max' => 100]),
                ],
                'dipakai' => $pakaiNeraca('kelas_kalori_id', ['batubara']),
            ],
            'status-penyelidikan' => [
                'grup' => 'Umum', 'model' => StatDikBb::class, 'label' => 'Status Penyelidikan (Batubara & Mineral)', 'urut' => 'id',
                'kolom' => [$teks('label', 'Label', ['unik' => true])],
                'dipakai' => $pakaiNeraca('stat_dik_bb_id', ['batubara', 'logam', 'bukan_logam']),
            ],
            'instansi' => [
                'grup' => 'Umum', 'model' => IdInstansi::class, 'label' => 'Instansi', 'urut' => 'label',
                'kolom' => [$teks('label', 'Nama Instansi', ['unik' => true])],
                'dipakai' => $pakaiNeraca('id_instansi_id', ['batubara', 'logam', 'bukan_logam', 'panas_bumi']),
            ],
            'status-penyelidikan-pb' => [
                'grup' => 'Panas Bumi', 'model' => StatDikPb::class, 'label' => 'Status Penyelidikan Panas Bumi', 'urut' => 'id',
                'kolom' => [$teks('label', 'Label', ['unik' => true])],
                'dipakai' => $pakaiNeraca('stat_dik_pb_id', ['panas_bumi']),
            ],
            'klasifikasi-temperatur' => [
                'grup' => 'Panas Bumi', 'model' => KlasifikasiTemperatur::class, 'label' => 'Klasifikasi Temperatur', 'urut' => 'id',
                'kolom' => [$teks('label', 'Label', ['unik' => true])],
                'dipakai' => $pakaiNeraca('klasifikasi_temperatur_id', ['panas_bumi']),
            ],
        ];
    }

    private function cfg(string $jenis): array
    {
        $semua = $this->jenis();
        abort_unless(isset($semua[$jenis]), 404);

        return $semua[$jenis] + ['slug' => $jenis];
    }

    public function index()
    {
        $grup = [];
        foreach ($this->jenis() as $slug => $cfg) {
            $grup[$cfg['grup']][] = ['slug' => $slug, 'label' => $cfg['label'], 'jumlah' => $cfg['model']::count()];
        }

        return view('admin.referensi.index', compact('grup'));
    }

    public function daftar(Request $request, string $jenis)
    {
        $cfg = $this->cfg($jenis);
        $model = $cfg['model'];

        $kolomTeks = collect($cfg['kolom'])->firstWhere('tipe', 'text')['k'] ?? null;

        $rows = $model::query()
            ->when($jenis === 'kabupaten', fn ($q) => $q->with('provinsi'))
            ->when($request->search && $kolomTeks, fn ($q) => $q->where($kolomTeks, 'like', '%' . $request->search . '%'))
            ->when($jenis === 'kabupaten' && $request->provinsi_id, fn ($q) => $q->where('provinsi_id', $request->provinsi_id))
            ->orderBy($cfg['urut'])
            ->paginate(15)
            ->withQueryString();

        return view('admin.referensi.daftar', [
            'cfg' => $cfg,
            'rows' => $rows,
            'relasiOpsi' => $this->relasiOpsi($cfg),
            'provinsis' => $jenis === 'kabupaten' ? Provinsi::orderBy('nama_provinsi')->get() : collect(),
        ]);
    }

    public function create(string $jenis)
    {
        $cfg = $this->cfg($jenis);

        return view('admin.referensi.form', ['cfg' => $cfg, 'row' => null, 'relasiOpsi' => $this->relasiOpsi($cfg)]);
    }

    public function store(Request $request, string $jenis)
    {
        $cfg = $this->cfg($jenis);
        $data = $request->validate($this->aturan($cfg, $request), $this->pesan());

        $cfg['model']::create($data);

        return redirect()->route('superuser.referensi.daftar', $jenis)
            ->with('success', $cfg['label'] . ' berhasil ditambahkan.');
    }

    public function edit(string $jenis, int $id)
    {
        $cfg = $this->cfg($jenis);
        $row = $cfg['model']::findOrFail($id);

        return view('admin.referensi.form', ['cfg' => $cfg, 'row' => $row, 'relasiOpsi' => $this->relasiOpsi($cfg)]);
    }

    public function update(Request $request, string $jenis, int $id)
    {
        $cfg = $this->cfg($jenis);
        $row = $cfg['model']::findOrFail($id);
        $data = $request->validate($this->aturan($cfg, $request, $row->id), $this->pesan());

        $row->update($data);

        return redirect()->route('superuser.referensi.daftar', $jenis)
            ->with('success', $cfg['label'] . ' berhasil diperbarui.');
    }

    public function destroy(string $jenis, int $id)
    {
        $cfg = $this->cfg($jenis);
        $row = $cfg['model']::findOrFail($id);

        $dipakai = [];
        foreach ($cfg['dipakai'] as [$tabel, $kolom, $label]) {
            $n = DB::table($tabel)->where($kolom, $row->id)->count();
            if ($n > 0) {
                $dipakai[] = number_format($n, 0, ',', '.') . " baris di {$label}";
            }
        }

        if ($dipakai) {
            return back()->with('error', 'Tidak bisa dihapus, masih dipakai oleh: ' . implode(', ', $dipakai) . '.');
        }

        $row->delete();

        return redirect()->route('superuser.referensi.daftar', $jenis)
            ->with('success', $cfg['label'] . ' berhasil dihapus.');
    }

    /** opsi dropdown untuk kolom bertipe relasi: [kolom => koleksi model] */
    private function relasiOpsi(array $cfg): array
    {
        $opsi = [];
        foreach ($cfg['kolom'] as $k) {
            if ($k['tipe'] === 'relasi') {
                $opsi[$k['k']] = $k['model']::orderBy($k['tampil'])->get();
            }
        }

        return $opsi;
    }

    private function aturan(array $cfg, Request $request, ?int $abaikanId = null): array
    {
        $tabel = (new $cfg['model'])->getTable();
        $aturan = [];

        foreach ($cfg['kolom'] as $k) {
            $r = [];

            if ($k['tipe'] === 'relasi') {
                $r = ['required', 'integer', Rule::exists((new $k['model'])->getTable(), 'id')];
            } elseif ($k['tipe'] === 'pilihan') {
                $r = ['nullable', Rule::in($k['pilihan'])];
            } else {
                $r = [($k['wajib'] ?? true) ? 'required' : 'nullable', 'string', 'max:' . ($k['max'] ?? 150)];

                if (! empty($k['unik'])) {
                    $r[] = Rule::unique($tabel, $k['k'])->ignore($abaikanId);
                }
                if (! empty($k['unik_dalam'])) {
                    $r[] = Rule::unique($tabel, $k['k'])
                        ->where($k['unik_dalam'], $request->input($k['unik_dalam']))
                        ->ignore($abaikanId);
                }
            }

            $aturan[$k['k']] = $r;
        }

        return $aturan;
    }

    private function pesan(): array
    {
        return [
            '*.required' => ':attribute wajib diisi.',
            '*.unique' => ':attribute ini sudah ada.',
            '*.in' => 'Pilihan tidak valid.',
            '*.exists' => 'Data induk tidak ditemukan.',
        ];
    }
}
