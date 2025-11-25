@extends('layouts.app')

@section('title', 'Detail Proyek')

@section('content')
<div class="page-header">
    <h2>Detail Proyek</h2>
    <ul class="breadcrumb">
        <li>Home</li>
        <li><a href="{{ route('proyek.index') }}">Kelola Proyek</a></li>
        <li>Detail Proyek</li>
    </ul>
</div>

<!-- INFO PROYEK LENGKAP -->
<div class="project-info" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: #2c3e50;">{{ $proyek->nama_proyek }}</h3>
        <small style="color: #7f8c8d;">
            Dibuat pada: {{ $proyek->created_at->format('d F Y H:i') }} | 
            Terakhir update: {{ $proyek->updated_at->format('d F Y H:i') }}
        </small>
    </div>
    
    <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
        
        <!-- Baris 1 -->
        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Sekolah</label>
            <span style="font-weight: 600; font-size: 1.1rem;">{{ $proyek->sekolah->nama_sekolah ?? 'Data Terhapus' }}</span>
        </div>
        
        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Status Proyek</label>
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
        </div>

        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Anggaran</label>
            <span style="font-weight: 600; font-size: 1.1rem; color: #2c3e50;">Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}</span>
        </div>

        <!-- Baris 2 -->
        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Penanggung Jawab (PJL)</label>
            <span style="font-weight: 600;">{{ $proyek->pjl->nama ?? '-' }}</span>
        </div>

        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Admin Pembuat</label>
            <span style="font-weight: 600;">{{ $proyek->admin->nama ?? '-' }}</span>
        </div>

        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Durasi Kontrak</label>
            @php
                $start = \Carbon\Carbon::parse($proyek->tanggal_mulai);
                $end = \Carbon\Carbon::parse($proyek->tanggal_selesai);
                $durasi = $start->diffInDays($end);
            @endphp
            <span style="font-weight: 600;">{{ $durasi }} Hari Kalender</span>
        </div>

        <!-- Baris 3 -->
        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Tanggal Mulai</label>
            <span style="font-weight: 600;">{{ date('d F Y', strtotime($proyek->tanggal_mulai)) }}</span>
        </div>
        
        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Tanggal Selesai</label>
            <span style="font-weight: 600;">{{ date('d F Y', strtotime($proyek->tanggal_selesai)) }}</span>
        </div>

        <div class="info-item">
            <label style="color: #7f8c8d; display: block; margin-bottom: 0.25rem;">Sisa Waktu</label>
            @php
                $today = \Carbon\Carbon::now();
                $sisa = $today->diffInDays($end, false);
                $textSisa = $sisa > 0 ? $sisa . ' Hari Lagi' : 'Waktu Habis / Selesai';
                $color = $sisa > 0 ? '#27ae60' : '#c0392b';
            @endphp
            <span style="font-weight: 600; color: {{ $color }};">{{ $textSisa }}</span>
        </div>

    </div>
    
    <div class="info-item" style="margin-top: 1.5rem; border-top: 1px solid #eee; padding-top: 1rem;">
        <label style="color: #7f8c8d; display: block; margin-bottom: 0.5rem;">Deskripsi Proyek</label>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; border: 1px solid #eee; line-height: 1.6;">
            {{ $proyek->deskripsi ?? 'Tidak ada deskripsi.' }}
        </div>
    </div>
</div>

<!-- STATISTIK PROGRES -->
<div class="progress-section" style="margin-top: 2rem; background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3>Progres Terkini (Berdasarkan Laporan Valid)</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="progress-item">
            <label>Progres Fisik</label>
            <div class="progress-bar"><div class="progress-fill" style="width: {{ $progresFisik }}%"></div></div>
            <span>{{ $progresFisik }}%</span>
        </div>
        <div class="progress-item">
            <label>Progres Keuangan</label>
            <div class="progress-bar"><div class="progress-fill" style="width: {{ $progresKeuangan }}%"></div></div>
            <span>{{ $progresKeuangan }}%</span>
        </div>
    </div>
</div>

<!-- TABEL RIWAYAT LAPORAN -->
<div class="table-container" style="margin-top: 2rem;">
    <div class="table-header">
        <h3>Riwayat Laporan Progres</h3>
        <div class="table-actions">
            {{-- LOGIKA PERBAIKAN: Tombol Tambah Laporan HILANG jika Pimpinan --}}
            @if(Auth::user()->role->nama_role !== 'Pimpinan')
                <a href="{{ route('laporan.create', $proyek->id) }}" class="btn btn-primary">
                    + Tambah Laporan Baru
                </a>
            @endif
            {{-- Akhir Logika --}}
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Fisik</th>
                <th>Keuangan</th>
                <th>Keterangan</th>
                <th>Dokumentasi</th>
                <th>Status Validasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proyek->laporan as $laporan)
            <tr>
                <td>{{ date('d/m/Y', strtotime($laporan->tanggal_laporan)) }}</td>
                <td>{{ $laporan->progres_fisik }}%</td>
                <td>{{ $laporan->progres_keuangan }}%</td>
                <td>{{ Str::limit($laporan->keterangan, 40) }}</td>
                
                <td>
                    @if($laporan->foto->count() > 0)
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <img src="{{ asset('storage/' . $laporan->foto->first()->file_path) }}" 
                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
                            @if($laporan->foto->count() > 1)
                                <span style="font-size: 11px; color: #666;">+{{ $laporan->foto->count() - 1 }}</span>
                            @endif
                        </div>
                    @else
                        <span style="color: #999; font-style: italic; font-size: 12px;">Tidak ada foto</span>
                    @endif
                </td>

                <td>
                    @php
                        $badgeClass = match($laporan->status_validasi) {
                            'Disetujui' => 'selesai',
                            'Ditolak' => 'terlambat',
                            default => 'berjalan'
                        };
                    @endphp
                    <span class="status {{ $badgeClass }}">
                        {{ $laporan->status_validasi ?? 'Menunggu' }}
                    </span>
                </td>

                <td>
                    <button class="btn btn-outline" 
                            onclick="showDetailLaporan(this)" 
                            data-json="{{ $laporan->load('foto', 'validator')->toJson() }}">
                        Detail
                    </button>

                    @if(in_array(Auth::user()->role->nama_role, ['Admin', 'Pimpinan']) && $laporan->status_validasi == 'Menunggu Validasi')
                        <div style="margin-top: 5px; display: flex; gap: 3px;">
                            <form action="{{ route('laporan.validasi', $laporan->id) }}" method="POST">
                                @csrf <input type="hidden" name="status" value="Disetujui">
                                <button type="submit" class="btn btn-success" style="padding: 2px 5px; font-size: 10px;" title="Setujui">✔</button>
                            </form>
                            <form action="{{ route('laporan.validasi', $laporan->id) }}" method="POST">
                                @csrf <input type="hidden" name="status" value="Ditolak">
                                <button type="submit" class="btn btn-danger" style="padding: 2px 5px; font-size: 10px;" title="Tolak">✖</button>
                            </form>
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #888;">Belum ada laporan progres.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- MODAL DETAIL LAPORAN (Sama seperti sebelumnya) -->
<div id="detailLaporanModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Detail Laporan Progres</h3>
            <button class="close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <div><label style="color: #7f8c8d; font-size: 12px;">Tanggal Laporan</label><div id="modalTanggal" style="font-weight: bold;">-</div></div>
                <div><label style="color: #7f8c8d; font-size: 12px;">Status Validasi</label><div id="modalStatus">-</div><small id="modalValidator" style="color: #666;"></small></div>
                <div><label style="color: #7f8c8d; font-size: 12px;">Progres Fisik</label><div id="modalFisik" style="font-weight: bold;">-</div></div>
                <div><label style="color: #7f8c8d; font-size: 12px;">Progres Keuangan</label><div id="modalKeuangan" style="font-weight: bold;">-</div></div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="color: #7f8c8d; font-size: 12px;">Keterangan / Catatan</label>
                <div id="modalKeterangan" style="background: #f9f9f9; padding: 10px; border-radius: 4px; border: 1px solid #eee; margin-top: 5px;">-</div>
            </div>
            <div>
                <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 10px; display: block;">Foto Dokumentasi</label>
                <div id="modalGallery" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    const storageBaseUrl = "{{ asset('storage') }}"; 
    function showDetailLaporan(button) {
        const data = JSON.parse(button.getAttribute('data-json'));
        document.getElementById('modalTanggal').innerText = new Date(data.tanggal_laporan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('modalFisik').innerText = data.progres_fisik + '%';
        document.getElementById('modalKeuangan').innerText = data.progres_keuangan + '%';
        document.getElementById('modalKeterangan').innerText = data.keterangan;
        
        const statusEl = document.getElementById('modalStatus');
        statusEl.innerText = data.status_validasi;
        statusEl.className = ''; 
        if(data.status_validasi == 'Disetujui') statusEl.classList.add('status', 'selesai');
        else if(data.status_validasi == 'Ditolak') statusEl.classList.add('status', 'terlambat');
        else statusEl.classList.add('status', 'berjalan');

        const valEl = document.getElementById('modalValidator');
        valEl.innerText = data.validator ? 'Divalidasi oleh: ' + data.validator.nama : '';

        const gallery = document.getElementById('modalGallery');
        gallery.innerHTML = ''; 
        if(data.foto && data.foto.length > 0) {
            data.foto.forEach(foto => {
                const imgContainer = document.createElement('a');
                imgContainer.href = `${storageBaseUrl}/${foto.file_path}`;
                imgContainer.target = '_blank';
                imgContainer.innerHTML = `<img src="${storageBaseUrl}/${foto.file_path}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">`;
                gallery.appendChild(imgContainer);
            });
        } else {
            gallery.innerHTML = '<p style="color:#999; font-style:italic;">Tidak ada foto dokumentasi.</p>';
        }
        document.getElementById('detailLaporanModal').style.display = 'flex';
    }
    function closeDetailModal() { document.getElementById('detailLaporanModal').style.display = 'none'; }
    window.onclick = function(event) { if (event.target == document.getElementById('detailLaporanModal')) closeDetailModal(); }
</script>
@endsection