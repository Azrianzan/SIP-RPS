<?php

// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Proyek;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung Statistik untuk Card
        $totalProyek = Proyek::count();
        $proyekBerjalan = Proyek::where('status_proyek', 'Berjalan')->count();
        $proyekSelesai = Proyek::where('status_proyek', 'Selesai')->count();
        $proyekTerlambat = Proyek::where('status_proyek', 'Terlambat')->count();

        // Ambil 5 proyek terbaru untuk tabel ringkasan
        $recentProyek = Proyek::with('sekolah')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalProyek', 'proyekBerjalan', 'proyekSelesai', 'proyekTerlambat', 'recentProyek'
        ));
    }
}