<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Proyek;
use App\Models\FotoLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    // Menampilkan Form
    public function create(Proyek $proyek)
    {
        return view('laporan.create', compact('proyek'));
    }

    // Menyimpan Data
    public function store(Request $request, Proyek $proyek)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal_laporan' => 'required|date',
            'progres_fisik' => 'required|numeric|min:0|max:100',
            'progres_keuangan' => 'required|numeric|min:0|max:100',
            
            // Validasi Dokumen Keuangan (Wajib, Format Dokumen, Max 5MB)
            'dokumen_keuangan' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:5120',
            
            'keterangan' => 'required|string',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::transaction(function () use ($request, $proyek) {
            
            // 2. Upload Dokumen Keuangan
            $pathDokumen = null;
            if ($request->hasFile('dokumen_keuangan')) {
                // Simpan ke folder 'laporan-keuangan' di storage public
                $pathDokumen = $request->file('dokumen_keuangan')->store('laporan-keuangan', 'public');
            }

            // 3. Simpan Data Laporan
            $laporan = Laporan::create([
                'proyek_id' => $proyek->id,
                'pelapor_id' => Auth::id(),
                'tanggal_laporan' => $request->tanggal_laporan,
                'progres_fisik' => $request->progres_fisik,
                'progres_keuangan' => $request->progres_keuangan,
                'dokumen_keuangan' => $pathDokumen, // Simpan path file di sini
                'keterangan' => $request->input('judul_laporan') ? $request->input('judul_laporan') . " - " . $request->keterangan : $request->keterangan,
                'status_validasi' => 'Menunggu Validasi',
            ]);

            // 4. Simpan Foto Dokumentasi (Jika ada)
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('laporan-foto', 'public');
                    FotoLaporan::create([
                        'laporan_id' => $laporan->id,
                        'file_path' => $path,
                        'deskripsi_foto' => 'Dokumentasi Progres'
                    ]);
                }
            }
        });

        return redirect()->route('proyek.show', $proyek->id)->with('success', 'Laporan berhasil ditambahkan');
    }

    public function validasi(Request $request, $id)
    {
        // Cek Hak Akses
        $userRole = Auth::user()->role->nama_role;
        if (!in_array($userRole, ['Admin', 'Pimpinan'])) {
            abort(403, 'Anda tidak berhak memvalidasi laporan.');
        }

        $laporan = Laporan::findOrFail($id);
        
        // Logika Validasi
        if ($request->status == 'Ditolak') {
            // Jika Ditolak, Alasan Wajib Diisi
            $request->validate([
                'alasan_penolakan' => 'required|string|max:1000'
            ], [
                'alasan_penolakan.required' => 'Wajib menyertakan alasan penolakan agar PJL bisa memperbaiki laporan.'
            ]);
            
            $laporan->alasan_penolakan = $request->alasan_penolakan;
        } else {
            // Jika Disetujui, kosongkan alasan (reset)
            $laporan->alasan_penolakan = null;
        }

        $laporan->status_validasi = $request->status;
        $laporan->validator_id = Auth::id();
        $laporan->save();

        return back()->with('success', 'Status laporan berhasil diperbarui menjadi: ' . $request->status);
    }

    // Halaman Utama Ekspor (Filter)
    public function indexEkspor(Request $request)
    {
        // Ambil semua proyek untuk dropdown filter
        $projects = Proyek::all();

        // Query Dasar
        $query = Laporan::with(['proyek', 'pelapor', 'validator']);

        // Filter Berdasarkan Proyek
        if ($request->has('proyek_id') && $request->proyek_id != '') {
            $query->where('proyek_id', $request->proyek_id);
        }

        // Filter Berdasarkan Tanggal Mulai
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal_laporan', '>=', $request->start_date);
        }

        // Filter Berdasarkan Tanggal Akhir
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal_laporan', '<=', $request->end_date);
        }

        // Ambil datanya
        $laporans = $query->latest('tanggal_laporan')->get();

        return view('laporan.ekspor', compact('laporans', 'projects'));
    }

    // Halaman Cetak (Print View -> PDF)
    public function printLaporan(Request $request)
    {
        // Logika query SAMA PERSIS dengan indexEkspor
        $query = Laporan::with(['proyek', 'pelapor']);

        if ($request->proyek_id) $query->where('proyek_id', $request->proyek_id);
        if ($request->start_date) $query->whereDate('tanggal_laporan', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('tanggal_laporan', '<=', $request->end_date);

        $laporans = $query->orderBy('tanggal_laporan', 'asc')->get();

        // Return ke view khusus print (tanpa navbar/sidebar)
        return view('laporan.print', compact('laporans'));
    }

    // 3. Download CSV (Excel)
    public function exportCsv(Request $request)
    {
        $filename = 'laporan-progres-' . date('Y-m-d') . '.csv';

        // Logika query SAMA
        $query = Laporan::with(['proyek', 'pelapor']);
        if ($request->proyek_id) $query->where('proyek_id', $request->proyek_id);
        if ($request->start_date) $query->whereDate('tanggal_laporan', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('tanggal_laporan', '<=', $request->end_date);
        $laporans = $query->orderBy('tanggal_laporan', 'asc')->get();

        // Header CSV
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Isi File CSV
        $callback = function() use($laporans) {
            $file = fopen('php://output', 'w');
            
            // Baris Judul Kolom
            fputcsv($file, ['Tanggal', 'Nama Proyek', 'Pelapor', 'Fisik (%)', 'Keuangan (%)', 'Status Validasi', 'Keterangan']);

            // Baris Data
            foreach ($laporans as $row) {
                fputcsv($file, [
                    $row->tanggal_laporan,
                    $row->proyek->nama_proyek,
                    $row->pelapor->nama,
                    $row->progres_fisik,
                    $row->progres_keuangan,
                    $row->status_validasi,
                    $row->keterangan
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}