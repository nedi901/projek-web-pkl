<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaPanasBumi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBatubara = NeracaBatubara::sum('total_sd');
        $totalMineralLogam = NeracaMineralLogam::sum('total_sd_bijih');
        $totalBukanLogam = NeracaMineralBukanLogam::sum('total_sd');
        $totalPanasBumi = NeracaPanasBumi::sum('total_sd');

        $jumlahLokasi = NeracaBatubara::count() + NeracaMineralLogam::count()
            + NeracaMineralBukanLogam::count() + NeracaPanasBumi::count();

        return view('user.dashboard', compact(
            'totalBatubara', 'totalMineralLogam', 'totalBukanLogam', 'totalPanasBumi', 'jumlahLokasi'
        ));
    }
}