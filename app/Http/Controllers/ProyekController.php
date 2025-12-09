<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyekController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Query Dasar
        $query = Proyek::with('sekolah');

        // 2. Logika PENCARIAN
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_proyek', 'like', "%{$search}%")
                  ->orWhereHas('sekolah', function($subQ) use ($search) {
                      $subQ->where('nama_sekolah', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Eksekusi Query
        $proyeks = $query->latest()->get();

        // Data untuk Dropdown Modal
        $sekolahs = Sekolah::all();
        $pjls = User::whereHas('role', function($q) {
            $q->where('nama_role', 'PJL');
        })->get();

        return view('proyek.index', compact('proyeks', 'sekolahs', 'pjls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'sekolah_id' => 'required|exists:sekolah,id',
            'pjl_id' => 'required|exists:users,id',
            // Validasi: Tidak boleh negatif
            'anggaran' => 'required|numeric|min:0', 
            'tanggal_mulai' => 'required|date',
            // Validasi: Tanggal selesai harus setelah atau sama dengan tanggal mulai
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai', 
            'deskripsi' => 'nullable|string',
        ], [
            // Custom Error Messages (Opsional, agar lebih jelas)
            'anggaran.min' => 'Nilai anggaran tidak boleh negatif.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $data = $request->all();
        $data['admin_id'] = $request->user()->id; 
        $data['status_proyek'] = 'Persiapan'; 

        Proyek::create($data);

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil dibuat');
    }

    public function show(Proyek $proyek)
    {
        $proyek->load(['sekolah', 'pjl', 'admin', 'laporan.foto' => function($q) {
            $q->latest(); 
        }]);

        // Ambil Laporan Valid Terakhir
        $laporanValidTerakhir = $proyek->laporan()
                                       ->where('status_validasi', 'Disetujui')
                                       ->orderBy('tanggal_laporan', 'desc')
                                       ->orderBy('updated_at', 'desc')
                                       ->first();

        $progresFisik = $laporanValidTerakhir ? $laporanValidTerakhir->progres_fisik : 0;
        $progresKeuangan = $laporanValidTerakhir ? $laporanValidTerakhir->progres_keuangan : 0;

        // PERUBAHAN: Tambahkan 'laporanValidTerakhir' ke compact agar bisa diakses di view
        return view('proyek.show', compact('proyek', 'progresFisik', 'progresKeuangan', 'laporanValidTerakhir'));
    }

    public function getData($id)
    {
        $proyek = Proyek::findOrFail($id);
        return response()->json($proyek);
    }

    public function update(Request $request, Proyek $proyek)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'sekolah_id' => 'required|exists:sekolah,id',
            'pjl_id' => 'required|exists:users,id',
            'anggaran' => 'required|numeric|min:0', // Tidak boleh negatif
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai', // Validasi tanggal
            'status_proyek' => 'required|string',
            'deskripsi' => 'nullable|string',
        ], [
            'anggaran.min' => 'Nilai anggaran tidak boleh negatif.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
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