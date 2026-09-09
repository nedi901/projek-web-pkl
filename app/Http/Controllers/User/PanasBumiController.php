<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaPanasBumi;
use Illuminate\Http\Request;

class PanasBumiController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaPanasBumi::with(['provinsi', 'kabupaten', 'statDikBb'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->when($request->provinsi_id, fn($q) => $q->where('provinsi_id', $request->provinsi_id))
            ->paginate(15);

        return view('user.panas-bumi.index', compact('data'));
    }

    public function show(NeracaPanasBumi $panasBumi)
    {
        $panasBumi->load(['provinsi', 'kabupaten', 'statDikBb', 'idInstansi']);
        return view('user.panas-bumi.show', compact('panasBumi'));
    }
}