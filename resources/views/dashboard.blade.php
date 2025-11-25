@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-header">
    <h2>Dashboard</h2>
    <ul class="breadcrumb">
        <li>SIP-RPS</li>
        <li>Dashboard</li>
    </ul>
</div>

@php
    $proyekPersiapan = $totalProyek - ($proyekBerjalan + $proyekSelesai + $proyekTerlambat);
@endphp

<div class="dashboard-cards">
    <!-- Card TOTAL -->
    <div class="card primary" onclick="showProjectList('Total')" style="cursor: pointer; transition: transform 0.2s;">
        <h3>Total Proyek</h3>
        <div class="value">{{ $totalProyek }}</div>
        <p>Semua proyek rehabilitasi</p>
    </div>

    <!-- Card PERSIAPAN -->
    <div class="card" onclick="showProjectList('Persiapan')" style="border-left: 5px solid #f39c12; cursor: pointer; transition: transform 0.2s;">
        <h3>Proyek Persiapan</h3>
        <div class="value">{{ $proyekPersiapan }}</div>
        <p>Dalam tahap perencanaan</p>
    </div>
    
    <!-- Card BERJALAN -->
    <div class="card success" onclick="showProjectList('Berjalan')" style="cursor: pointer; transition: transform 0.2s;">
        <h3>Proyek Berjalan</h3>
        <div class="value">{{ $proyekBerjalan }}</div>
        <p>Dalam tahap pengerjaan</p>
    </div>
    
    <!-- Card SELESAI -->
    <div class="card warning" onclick="showProjectList('Selesai')" style="cursor: pointer; transition: transform 0.2s;">
        <h3>Proyek Selesai</h3>
        <div class="value">{{ $proyekSelesai }}</div>
        <p>Telah diselesaikan</p>
    </div>
    
    <!-- Card TERLAMBAT -->
    <div class="card danger" onclick="showProjectList('Terlambat')" style="cursor: pointer; transition: transform 0.2s;">
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
                Halaman Kelola Proyek
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

<!-- MODAL DAFTAR PROYEK (POP UP) -->
<div id="dashboardListModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 id="modalListTitle">Daftar Proyek</h3>
            <button class="close" onclick="closeDashboardModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="table-container" style="margin-top: 0; box-shadow: none;">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Nama Proyek</th>
                            <th>Sekolah</th>
                            <th>Anggaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="modalListBody">
                        <!-- Data akan diisi lewat JS -->
                        <tr><td colspan="5" style="text-align:center">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeDashboardModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- Grafik Chart.js ---
        const total = {{ $totalProyek }};
        const berjalan = {{ $proyekBerjalan }};
        const selesai = {{ $proyekSelesai }};
        const terlambat = {{ $proyekTerlambat }};
        const persiapan = {{ $proyekPersiapan }};

        const ctx = document.getElementById('projectChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Persiapan', 'Berjalan', 'Selesai', 'Terlambat'],
                datasets: [{
                    label: 'Jumlah Proyek',
                    data: [persiapan, berjalan, selesai, terlambat],
                    backgroundColor: ['#f39c12', '#3498db', '#2ecc71', '#e74c3c'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 14 }, padding: 20 } },
                    title: { display: false }
                }
            }
        });
    });

    // --- Logic Pop Up Dashboard ---
    const modal = document.getElementById('dashboardListModal');
    const modalTitle = document.getElementById('modalListTitle');
    const modalBody = document.getElementById('modalListBody');

    function showProjectList(status) {
        // Set Judul Modal
        modalTitle.innerText = 'Daftar Proyek: ' + status;
        modalBody.innerHTML = '<tr><td colspan="5" style="text-align:center">Sedang mengambil data...</td></tr>';
        modal.style.display = 'flex';

        // Fetch Data dari Server
        fetch(`/dashboard/detail/${status}`)
            .then(response => response.json())
            .then(data => {
                modalBody.innerHTML = ''; // Bersihkan loading

                if (data.length === 0) {
                    modalBody.innerHTML = '<tr><td colspan="5" style="text-align:center">Tidak ada data proyek untuk status ini.</td></tr>';
                    return;
                }

                // Loop data dan buat baris tabel
                data.forEach(proyek => {
                    // Format Rupiah
                    const anggaran = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(proyek.anggaran);
                    
                    // Tentukan warna status
                    let statusClass = '';
                    if(proyek.status_proyek === 'Berjalan') statusClass = 'berjalan';
                    else if(proyek.status_proyek === 'Selesai') statusClass = 'selesai';
                    else if(proyek.status_proyek === 'Terlambat') statusClass = 'terlambat';
                    else statusClass = 'warning'; // Persiapan

                    const row = `
                        <tr>
                            <td>${proyek.nama_proyek}</td>
                            <td>${proyek.sekolah ? proyek.sekolah.nama_sekolah : '-'}</td>
                            <td>${anggaran}</td>
                            <td><span class="status ${statusClass}">${proyek.status_proyek}</span></td>
                            <td>
                                <a href="/kelola-proyek/${proyek.id}" class="btn btn-outline btn-sm" target="_blank">Detail</a>
                            </td>
                        </tr>
                    `;
                    modalBody.innerHTML += row;
                });
            })
            .catch(error => {
                console.error(error);
                modalBody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat data.</td></tr>';
            });
    }

    function closeDashboardModal() {
        modal.style.display = 'none';
    }

    // Efek Hover Card agar user tahu bisa diklik
    const cards = document.querySelectorAll('.dashboard-cards .card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'scale(1.02)';
            card.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'scale(1)';
            card.style.boxShadow = '';
        });
    });

    window.onclick = function(event) {
        if (event.target == modal) {
            closeDashboardModal();
        }
    }
</script>
@endsection