<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use App\Models\NeracaGambut;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaPanasBumi;
use App\Models\RiwayatData;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Histori & Log Aktivitas gabungan semua domain (Super User).
 * Tombol Pulihkan / Hapus Permanen memakai route per-domain yang sudah ada (admin.<domain>.histori.*).
 */
class HistoriSemuaController extends Controller
{
    public const DOMAINS = [
        'batubara' => ['label' => 'Batubara', 'model' => NeracaBatubara::class, 'route' => 'admin.batubara'],
        'mineral_logam' => ['label' => 'Mineral Logam', 'model' => NeracaMineralLogam::class, 'route' => 'admin.mineral-logam'],
        'mineral_bukan_logam' => ['label' => 'Mineral Bukan Logam', 'model' => NeracaMineralBukanLogam::class, 'route' => 'admin.mineral-bukan-logam'],
        'panas_bumi' => ['label' => 'Panas Bumi', 'model' => NeracaPanasBumi::class, 'route' => 'admin.panas-bumi'],
        'gambut' => ['label' => 'Gambut', 'model' => NeracaGambut::class, 'route' => 'admin.gambut'],
    ];

    public function index(Request $request)
    {
        $tab = $request->query('tab') === 'log' ? 'log' : 'data';
        $domain = isset(self::DOMAINS[$request->query('domain')]) ? $request->query('domain') : null;

        return $tab === 'log' ? $this->log($request, $domain) : $this->dataTerhapus($request, $domain);
    }

    private function dataTerhapus(Request $request, ?string $domain)
    {
        $cari = $request->query('search');
        $items = collect();

        foreach (self::DOMAINS as $slug => $cfg) {
            if ($domain && $domain !== $slug) {
                continue;
            }

            $rows = $cfg['model']::onlyTrashed()
                ->with(['provinsi', 'deletedBy'])
                ->when($cari, fn ($q) => $q->where('nama_objek', 'like', "%{$cari}%"))
                ->orderByDesc('deleted_at')
                ->limit(300)
                ->get();

            foreach ($rows as $row) {
                $items->push(['label' => $cfg['label'], 'route' => $cfg['route'], 'row' => $row]);
            }
        }

        $items = $items->sortByDesc(fn ($i) => $i['row']->deleted_at)->values();

        $perHalaman = 15;
        $halaman = LengthAwarePaginator::resolveCurrentPage();
        $data = new LengthAwarePaginator(
            $items->forPage($halaman, $perHalaman)->values(),
            $items->count(),
            $perHalaman,
            $halaman,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.histori-semua.index', ['tab' => 'data', 'data' => $data, 'domain' => $domain, 'domains' => self::DOMAINS]);
    }

    private function log(Request $request, ?string $domain)
    {
        $cari = $request->query('search');

        $data = RiwayatData::query()
            ->when($domain, fn ($q) => $q->where('domain', $domain))
            ->when($request->query('aksi'), fn ($q, $aksi) => $q->where('aksi', $aksi))
            ->when($cari, function ($q) use ($cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('nama_data', 'like', "%{$cari}%")->orWhere('user_nama', 'like', "%{$cari}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.histori-semua.index', ['tab' => 'log', 'data' => $data, 'domain' => $domain, 'domains' => self::DOMAINS]);
    }
}
