@extends('layouts.app')

@section('title', 'Ekspor Laporan')

@section('content')
<div class="page-header">
    <h2>Ekspor Laporan</h2>
    <ul class="breadcrumb">
        <li>Home</li>
        <li>Ekspor Laporan</li>
    </ul>
</div>

<div class="filter-section" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1rem;">Filter Laporan</h3>
    
    <!-- Form Filter (GET Request ke halaman ini sendiri) -->
    <form method="GET" action="{{ route('ekspor.index') }}">
        <div class="filter-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            
            <div class="form-group">
                <label for="project-filter">Proyek</label>
                <select name="proyek_id" id="project-filter" class="form-control" style="width: 100%; padding: 8px;">
                    <option value="">Semua Proyek</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('proyek_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->nama_proyek }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="start-date">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start-date" class="form-control" value="{{ request('start_date') }}" style="width: 100%; padding: 8px;">
            </div>
            
            <div class="form-group">
                <label for="end-date">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end-date" class="form-control" value="{{ request('end_date') }}" style="width: 100%; padding: 8px;">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%;">🔍 Terapkan Filter</button>
            </div>
        </div>
    </form>
    
    <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid #eee;">

    <div class="export-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
        <!-- Tombol Print (PDF) - Membawa parameter filter saat ini -->
        <a href="{{ route('ekspor.print', request()->query()) }}" target="_blank" class="btn btn-danger">
            📄 Cetak / PDF
        </a>
        
        <!-- Tombol Excel (CSV) - Membawa parameter filter saat ini -->
        <a href="{{ route('ekspor.csv', request()->query()) }}" target="_blank" class="btn btn-success">
            📊 Ekspor Excel (CSV)
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Data Laporan Progres ({{ $laporans->count() }} Data)</h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Proyek</th>
                <th>Penanggung Jawab</th>
                <th>Fisik</th>
                <th>Keuangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $laporan)
            <tr>
                <td>{{ date('d/m/Y', strtotime($laporan->tanggal_laporan)) }}</td>
                <td>{{ $laporan->proyek->nama_proyek }}</td>
                <td>{{ $laporan->pelapor->nama }}</td>
                <td>{{ $laporan->progres_fisik }}%</td>
                <td>{{ $laporan->progres_keuangan }}%</td>
                <td>
                    @php
                        $statusClass = match($laporan->status_validasi) {
                            'Disetujui' => 'selesai',
                            'Ditolak' => 'terlambat',
                            default => 'berjalan'
                        };
                    @endphp
                    <span class="status {{ $statusClass }}">{{ $laporan->status_validasi }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center">Tidak ada data laporan yang sesuai filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection