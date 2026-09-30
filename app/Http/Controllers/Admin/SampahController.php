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

/**
 * Satu controller untuk halaman Sampah + Riwayat semua domain.
 * Domain-nya datang dari route ->defaults('domain', '<slug>') di routes/web.php,
 * dan akses per-domain sudah dijaga middleware admin.* pada route group masing-masing.
 */
class SampahController extends Controller
{
    private const DOMAINS = [
        'batubara' => [
            'model' => NeracaBatubara::class,
            'label' => 'Batubara',
            'route' => 'admin.batubara',
        ],
        'mineral_logam' => [
            'model' => NeracaMineralLogam::class,
            'label' => 'Mineral Logam',
            'route' => 'admin.mineral-logam',
        ],
        'mineral_bukan_logam' => [
            'model' => NeracaMineralBukanLogam::class,
            'label' => 'Mineral Bukan Logam',
            'route' => 'admin.mineral-bukan-logam',
        ],
        'panas_bumi' => [
            'model' => NeracaPanasBumi::class,
            'label' => 'Panas Bumi',
            'route' => 'admin.panas-bumi',
        ],
        'gambut' => [
            'model' => NeracaGambut::class,
            'label' => 'Gambut',
            'route' => 'admin.gambut',
        ],
    ];

    private function config(Request $request): array
    {
        $slug = $request->route('domain');
        abort_unless(isset(self::DOMAINS[$slug]), 404);

        return self::DOMAINS[$slug] + ['slug' => $slug];
    }

    public function index(Request $request)
    {
        $cfg = $this->config($request);
        $model = $cfg['model'];

        $data = $model::onlyTrashed()
            ->with(['provinsi', 'deletedBy'])
            ->when($request->search, fn ($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.sampah.index', [
            'data' => $data,
            'label' => $cfg['label'],
            'routePrefix' => $cfg['route'],
        ]);
    }

    public function restore(Request $request, $id)
    {
        $cfg = $this->config($request);

        $row = $cfg['model']::onlyTrashed()->findOrFail($id);
        $row->restore();

        return redirect()->route($cfg['route'] . '.sampah.index')
            ->with('success', "Data \"{$row->nama_objek}\" berhasil dipulihkan.");
    }

    public function forceDestroy(Request $request, $id)
    {
        // Hapus permanen cuma untuk super_user. Admin domain cukup bisa hapus (ke sampah) & pulihkan.
        abort_unless(auth()->user()?->role === 'super_user', 403, 'Hapus permanen hanya boleh dilakukan Super User.');

        $cfg = $this->config($request);

        $row = $cfg['model']::onlyTrashed()->findOrFail($id);
        $nama = $row->nama_objek;
        $row->forceDelete();

        return redirect()->route($cfg['route'] . '.sampah.index')
            ->with('success', "Data \"{$nama}\" dihapus permanen.");
    }

    public function riwayat(Request $request)
    {
        $cfg = $this->config($request);

        $data = RiwayatData::where('domain', $cfg['slug'])
            ->when($request->aksi, fn ($q) => $q->where('aksi', $request->aksi))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.sampah.riwayat', [
            'data' => $data,
            'label' => $cfg['label'],
            'routePrefix' => $cfg['route'],
        ]);
    }
}
