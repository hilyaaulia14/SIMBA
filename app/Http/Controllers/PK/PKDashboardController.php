<?php

namespace App\Http\Controllers\PK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\JadwalBimbingan;
use App\Models\Klien;

class PKDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        // 3 Metric Cards required by acceptance criteria
        $jadwalHariIni = JadwalBimbingan::whereDate('tgl_bimbingan', $today)->count();
        $jadwalMingguIni = JadwalBimbingan::whereBetween('tgl_bimbingan', [$startOfWeek, $endOfWeek])->count();
        $totalKlien = Klien::count();

        // Tabel ringkasan agenda bimbingan klien terdekat
        $agendaTerdekat = JadwalBimbingan::with(['klien', 'statusBimbingan', 'pegawai'])
            ->whereDate('tgl_bimbingan', '>=', $today)
            ->orderBy('tgl_bimbingan', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(6)
            ->get();

        return view('pk.dashboard', compact(
            'jadwalHariIni',
            'jadwalMingguIni',
            'totalKlien',
            'agendaTerdekat'
        ));
    }
}
