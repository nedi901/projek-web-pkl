<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TabelReferensi;
use App\Support\TabelParser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

/**
 * Kelola Tabel Buku (Super User): tambah/ubah/hapus tabel rekapitulasi yang tampil di halaman
 * "Grafik dan Visualisasi" tiap domain. Isi tabel ditempel dari Excel (dipisah TAB), lihat TabelParser.
 */
class TabelBukuController extends Controller
{
    public const DOMAINS = [
        'batubara' => ['Batubara', 'admin.batubara'],
        'mineral_logam' => ['Mineral Logam', 'admin.mineral-logam'],
        'mineral_bukan_logam' => ['Mineral Bukan Logam', 'admin.mineral-bukan-logam'],
        'panas_bumi' => ['Panas Bumi', 'admin.panas-bumi'],
        'gambut' => ['Gambut', 'admin.gambut'],
    ];

    public function index(Request $request)
    {
        $domain = isset(self::DOMAINS[$request->query('domain')]) ? $request->query('domain') : null;

        $tabels = TabelReferensi::query()
            ->when($domain, fn ($q) => $q->where('domain', $domain))
            ->when($request->query('search'), fn ($q, $s) => $q->where('judul', 'like', "%{$s}%"))
            ->orderBy('domain')->orderBy('urutan')->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tabel-buku.index', ['tabels' => $tabels, 'domain' => $domain, 'domains' => self::DOMAINS]);
    }

    public function create()
    {
        return view('admin.tabel-buku.form', ['tabel' => null, 'teks' => '', 'domains' => self::DOMAINS]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());

        try {
            $hasil = TabelParser::dariTeks($data['data']);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['data' => $e->getMessage()]);
        }

        $tabel = TabelReferensi::create([
            'kode' => $this->kodeUnik($data['kode'] ?? null, $data['judul'], $data['tahun_data'] ?? null),
            'domain' => $data['domain'],
            'judul' => $data['judul'],
            'tahun_data' => $data['tahun_data'] ?? null,
            'sumber' => $data['sumber'] ?? null,
            'urutan' => $data['urutan'] ?? 99,
            'catatan' => $data['catatan'] ?? null,
            'kolom' => $hasil['kolom'],
            'baris' => $hasil['baris'],
        ]);

        return redirect()->route('superuser.tabel-buku.index')
            ->with('success', "Tabel \"{$tabel->judul}\" berhasil ditambahkan.");
    }

    public function edit(TabelReferensi $tabel)
    {
        return view('admin.tabel-buku.form', [
            'tabel' => $tabel,
            'teks' => TabelParser::keTeks($tabel->kolom, $tabel->baris),
            'domains' => self::DOMAINS,
        ]);
    }

    public function update(Request $request, TabelReferensi $tabel)
    {
        $data = $request->validate($this->aturan($tabel));

        try {
            $hasil = TabelParser::dariTeks($data['data'], $tabel->baris, $tabel->kolom);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['data' => $e->getMessage()]);
        }

        $tabel->update([
            'domain' => $data['domain'],
            'judul' => $data['judul'],
            'tahun_data' => $data['tahun_data'] ?? null,
            'sumber' => $data['sumber'] ?? null,
            'urutan' => $data['urutan'] ?? 99,
            'catatan' => $data['catatan'] ?? null,
            'kolom' => $hasil['kolom'],
            'baris' => $hasil['baris'],
        ]);

        return redirect()->route('superuser.tabel-buku.index')
            ->with('success', "Tabel \"{$tabel->judul}\" berhasil diperbarui.");
    }

    public function destroy(TabelReferensi $tabel)
    {
        $judul = $tabel->judul;
        $tabel->delete();

        return redirect()->route('superuser.tabel-buku.index')
            ->with('success', "Tabel \"{$judul}\" dihapus.");
    }

    private function aturan(?TabelReferensi $tabel = null): array
    {
        return [
            'domain' => ['required', Rule::in(array_keys(self::DOMAINS))],
            'kode' => ['nullable', 'string', 'max:80', 'alpha_dash:ascii', Rule::unique('tabel_referensis', 'kode')->ignore($tabel?->id)],
            'judul' => ['required', 'string', 'max:255'],
            'tahun_data' => ['nullable', 'integer', 'between:1990,2100'],
            'sumber' => ['nullable', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'between:0,999'],
            'catatan' => ['nullable', 'string', 'max:3000'],
            'data' => ['required', 'string', 'max:200000'],
        ];
    }

    private function kodeUnik(?string $kode, string $judul, $tahun): string
    {
        $dasar = Str::lower($kode ?: Str::slug(Str::limit($judul, 50, ''), '-') . ($tahun ? "-{$tahun}" : ''));
        $dasar = $dasar !== '' ? $dasar : 'tabel';
        $kode = $dasar;
        $i = 2;

        while (TabelReferensi::where('kode', $kode)->exists()) {
            $kode = $dasar . '-' . $i++;
        }

        return $kode;
    }
}
