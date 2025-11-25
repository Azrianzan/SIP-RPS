@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Load Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-header">
    <h2>Dashboard</h2>
    <ul class="breadcrumb">
        <li>Home</li>
        <li>Dashboard</li>
    </ul>
</div>

{{-- Hitung data Persiapan di Blade --}}
@php
    $proyekPersiapan = $totalProyek - ($proyekBerjalan + $proyekSelesai + $proyekTerlambat);
@endphp

<div class="dashboard-cards">
    <div class="card primary">
        <h3>Total Proyek</h3>
        <div class="value">{{ $totalProyek }}</div>
        <p>Semua proyek rehabilitasi</p>
    </div>

    {{-- Card Baru: Proyek Persiapan --}}
    <div class="card" style="border-left: 5px solid #f39c12;">
        <h3>Proyek Persiapan</h3>
        <div class="value">{{ $proyekPersiapan }}</div>
        <p>Dalam tahap perencanaan</p>
    </div>
    
    <div class="card success">
        <h3>Proyek Berjalan</h3>
        <div class="value">{{ $proyekBerjalan }}</div>
        <p>Dalam tahap pengerjaan</p>
    </div>
    
    <div class="card warning">
        <h3>Proyek Selesai</h3>
        <div class="value">{{ $proyekSelesai }}</div>
        <p>Telah diselesaikan</p>
    </div>
    
    <div class="card danger">
        <h3>Proyek Terlambat</h3>
        <div class="value">{{ $proyekTerlambat }}</div>
        <p>Memerlukan perhatian</p>
    </div>
</div>

<!-- Container Grafik -->
<div class="card" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3>Statistik Status Proyek</h3>
    </div>
    <div style="position: relative; height: 350px; width: 100%; display: flex; justify-content: center;">
        <canvas id="projectChart"></canvas>
    </div>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <div class="table-header">
        <h3>Proyek Terkini</h3>
        <div class="table-actions">
            <a href="{{ route('proyek.index') }}" class="btn btn-primary">
                Lihat Semua
            </a>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nama Proyek</th>
                <th>Sekolah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentProyek as $proyek)
            <tr>
                <td>{{ $proyek->nama_proyek }}</td>
                <td>{{ $proyek->sekolah->nama_sekolah ?? '-' }}</td>
                <td>
                    @php
                        $statusClass = match($proyek->status_proyek) {
                            'Berjalan' => 'berjalan',
                            'Selesai' => 'selesai',
                            'Terlambat' => 'terlambat',
                            'Persiapan' => 'warning',
                            default => ''
                        };
                    @endphp
                    <span class="status {{ $statusClass }}">{{ $proyek->status_proyek }}</span>
                </td>
                <td>
                    <!-- Tombol Detail diarahkan ke route yang benar -->
                    <a href="{{ route('proyek.show', $proyek->id) }}" class="btn btn-outline">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center">Belum ada data proyek.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Hitung Data Persiapan (Total - (Berjalan + Selesai + Terlambat))
        // Kita parsing data dari PHP ke JS
        const total = {{ $totalProyek }};
        const berjalan = {{ $proyekBerjalan }};
        const selesai = {{ $proyekSelesai }};
        const terlambat = {{ $proyekTerlambat }};
        const persiapan = {{ $proyekPersiapan }}; // Ambil langsung dari variable PHP yang sudah kita hitung

        const ctx = document.getElementById('projectChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut', // Tipe grafik: Doughnut (Donat)
            data: {
                labels: ['Persiapan', 'Berjalan', 'Selesai', 'Terlambat'],
                datasets: [{
                    label: 'Jumlah Proyek',
                    data: [persiapan, berjalan, selesai, terlambat],
                    backgroundColor: [
                        '#f39c12', // Persiapan (Kuning/Oranye)
                        '#3498db', // Berjalan (Biru)
                        '#2ecc71', // Selesai (Hijau)
                        '#e74c3c'  // Terlambat (Merah)
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 14
                            },
                            padding: 20
                        }
                    },
                    title: {
                        display: false,
                        text: 'Distribusi Status Proyek'
                    }
                }
            }
        });
    });
</script>
@endsection