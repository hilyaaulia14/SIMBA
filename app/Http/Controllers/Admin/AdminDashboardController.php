<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalSuratMasuk = SuratMasuk::count();
        $totalSuratKeluar = SuratKeluar::count();
        $totalJadwal = \App\Models\JadwalBimbingan::count();
        
        // Total Berkas Terarsip Digital
        $totalArsipDigital = SuratMasuk::whereNotNull('upload_dokumen')->where('upload_dokumen', '!=', '')->count()
                           + SuratKeluar::whereNotNull('upload_dokumen')->where('upload_dokumen', '!=', '')->count();

        // Recent letters
        $recentSuratMasuk = SuratMasuk::with('surat')->latest('tgl_masuk')->take(5)->get();
        $recentSuratKeluar = SuratKeluar::with('surat')->latest('tgl_keluar')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSuratMasuk',
            'totalSuratKeluar',
            'totalJadwal',
            'totalArsipDigital',
            'recentSuratMasuk',
            'recentSuratKeluar'
        ));
    }
}
