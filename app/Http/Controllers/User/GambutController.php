<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaGambut;
use Illuminate\Http\Request;

class GambutController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaGambut::with(['provinsi', 'kabupaten'])
            ->when($request->search, fn ($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->when($request->provinsi_id, fn ($q) => $q->where('provinsi_id', $request->provinsi_id))
            ->orderBy('provinsi_id')
            ->paginate(15)
            ->withQueryString();

        return view('user.gambut.index', compact('data'));
    }

    public function show(NeracaGambut $gambut)
    {
        $gambut->load(['provinsi', 'kabupaten']);

        return view('user.gambut.show', compact('gambut'));
    }
}
