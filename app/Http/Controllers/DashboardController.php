<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Warga;
use App\Models\FasilitasUmum;
use App\Models\PeminjamanFasilitas;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalFasilitas   = FasilitasUmum::count();
        $totalWarga       = Warga::count();
        $totalKapasitas   = FasilitasUmum::sum('kapasitas'); 
        $peminjamanAktif  = PeminjamanFasilitas::where('status', 'disetujui')
                            ->where('tanggal_selesai', '>=', now())
                            ->count();
        $jenisStats = FasilitasUmum::select('jenis', DB::raw('count(*) as total'))
                     ->groupBy('jenis')
                     ->pluck('total', 'jenis')->toArray();
        $labelJenis = array_keys($jenisStats); 
        $dataJenis  = array_values($jenisStats); 
        $peminjamanPerBulan = PeminjamanFasilitas::select(
                        DB::raw('MONTH(tanggal_mulai) as bulan'), 
                        DB::raw('count(*) as total')
                    )
                    ->whereYear('tanggal_mulai', date('Y'))
                    ->groupBy('bulan')
                    ->pluck('total', 'bulan')->toArray();

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $peminjamanPerBulan[$i] ?? 0;
        }
        $fasilitasTerbaru = FasilitasUmum::latest()->limit(5)->get();
        
        $peminjamanTerbaru = PeminjamanFasilitas::with(['warga', 'fasilitas'])
                             ->latest()
                             ->limit(5)
                             ->get();

        return view('pages.dashboard', compact(
            'totalFasilitas', 'peminjamanAktif', 'totalWarga', 'totalKapasitas',
            'labelJenis', 'dataJenis', 'dataBulanan',
            'fasilitasTerbaru', 'peminjamanTerbaru'
        ));
    }
}