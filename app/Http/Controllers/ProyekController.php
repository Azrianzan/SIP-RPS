<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyekController extends Controller
{
    public function index()
    {
        // Ambil proyek dengan relasi sekolah
        $proyeks = Proyek::with('sekolah')->get();
        
        // Data untuk modal tambah (Dropdown Sekolah & User PJL)
        $sekolahs = Sekolah::all();
        // Ambil user yang rolenya PJL (Role ID disesuaikan/dicari berdasarkan nama)
        $pjls = User::whereHas('role', function($q){
            $q->where('nama_role', 'PJL');
        })->get();

        return view('proyek.index', compact('proyeks', 'sekolahs', 'pjls'));
    }

    public function getData($id)
    {
        $proyek = Proyek::findOrFail($id);
        return response()->json($proyek);
    }
    
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'sekolah_id' => 'required|exists:sekolah,id',
            'pjl_id' => 'required|exists:users,id',
            'anggaran' => 'required|numeric',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
        ]);

        // 2. Ambil semua data input
        $data = $request->all();
        
        // 3. PERBAIKAN DI SINI: Gunakan $request->user()->id
        // Ini lebih aman karena mengambil user langsung dari request yang sedang berjalan
        $data['admin_id'] = $request->user()->id; 
        
        $data['status_proyek'] = 'Persiapan'; 

        // 4. Simpan ke database
        Proyek::create($data);

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil dibuat');
    }

    // Method SHOW tetap ada (untuk halaman Detail)
    public function show(Proyek $proyek)
    {
        // Load relasi untuk tampilan (tetap sama)
        $proyek->load(['sekolah', 'pjl', 'admin', 'laporan.foto' => function($q) {
            $q->latest(); 
        }]);

        // Mengambil Progres Terkini
        $laporanValidTerakhir = $proyek->laporan()
                                       ->where('status_validasi', 'Disetujui') // WAJIB Disetujui
                                       ->orderBy('tanggal_laporan', 'desc')    // Prioritas 1: Tanggal Laporan paling baru
                                       ->orderBy('updated_at', 'desc')         // Prioritas 2: Jika tanggal sama, ambil yang baru diupdate
                                       ->first();

        // Set nilai progres
        $progresFisik = $laporanValidTerakhir ? $laporanValidTerakhir->progres_fisik : 0;
        $progresKeuangan = $laporanValidTerakhir ? $laporanValidTerakhir->progres_keuangan : 0;

        return view('proyek.show', compact('proyek', 'progresFisik', 'progresKeuangan'));
    }

    public function edit(Proyek $proyek)
    {
        $sekolahs = Sekolah::all();
        $pjls = User::whereHas('role', function($q) {
            $q->where('nama_role', 'PJL');
        })->get();

        return view('proyek.edit', compact('proyek', 'sekolahs', 'pjls'));
    }

    public function update(Request $request, Proyek $proyek)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'sekolah_id' => 'required|exists:sekolah,id',
            'pjl_id' => 'required|exists:users,id',
            'anggaran' => 'required|numeric',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status_proyek' => 'required|string',
            'deskripsi' => 'nullable|string',
        ]);

        $proyek->update($request->all());

        return redirect()->route('proyek.index')->with('success', 'Data proyek diperbarui');
    }

    public function destroy(Proyek $proyek)
    {
        $proyek->delete();
        return redirect()->route('proyek.index')->with('success', 'Proyek dihapus');
    }
}
