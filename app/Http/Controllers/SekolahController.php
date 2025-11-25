<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar
        $query = Sekolah::withCount('proyek'); // Hitung jumlah proyek otomatis

        // Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_sekolah', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%");
        }

        // Filter Kabupaten
        if ($request->has('kabupaten') && $request->kabupaten != '') {
            $query->where('kabupaten_kota', $request->kabupaten);
        }

        $sekolahs = $query->paginate(10); // Pagination

        // Statistik untuk Card
        $totalSekolah = Sekolah::count();
        $sekolahProyekAktif = Sekolah::whereHas('proyek', function($q){
            $q->where('status_proyek', 'Berjalan');
        })->count();
        // Contoh logika sederhana membedakan Kota/Kab
        $sekolahKota = Sekolah::where('kabupaten_kota', 'like', 'Kota%')
                              ->orWhere('kabupaten_kota', 'Banjarmasin')
                              ->orWhere('kabupaten_kota', 'Banjarbaru')->count();
        $sekolahKab = $totalSekolah - $sekolahKota;

        return view('sekolah.index', compact('sekolahs', 'totalSekolah', 'sekolahProyekAktif', 'sekolahKota', 'sekolahKab'));
    }

    public function store(Request $request)
    {
        // Validasi dan Simpan (Sederhana)
        Sekolah::create($request->all());
        return redirect()->back()->with('success', 'Sekolah berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $sekolah->update($request->all());
        return redirect()->back()->with('success', 'Data sekolah diperbarui');
    }

    public function destroy($id)
    {
        Sekolah::destroy($id);
        return redirect()->back()->with('success', 'Sekolah dihapus');
    }

    // Method KHUSUS untuk dipanggil via AJAX/Fetch JS di Modal Detail/Edit
    public function show($id)
    {
        $sekolah = Sekolah::with('proyek')->findOrFail($id);
        return response()->json($sekolah);
    }
}