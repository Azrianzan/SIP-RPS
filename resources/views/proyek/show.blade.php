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
        <!-- Progres Fisik -->
        <div class="progress-item">
            <label>Progres Fisik</label>
            <div class="progress-bar"><div class="progress-fill" style="width: {{ $progresFisik }}%"></div></div>
            <span>{{ $progresFisik }}%</span>
        </div>

        <!-- Progres Keuangan -->
        <div class="progress-item">
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <label>Progres Keuangan</label>
                @if(isset($laporanValidTerakhir) && $laporanValidTerakhir->dokumen_keuangan)
                    <a href="{{ asset('storage/' . $laporanValidTerakhir->dokumen_keuangan) }}" target="_blank" class="btn-link" style="font-size: 0.8rem; text-decoration: none; color: #2980b9; font-weight: 600;">
                        📄 Lihat Dokumen Bukti
                    </a>
                @endif
            </div>
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
            @if(Auth::user()->role->nama_role === 'PJL')
                <a href="{{ route('laporan.create', $proyek->id) }}" class="btn btn-primary">
                    + Tambah Laporan Baru
                </a>
            @endif
        </div>
    </div>
    
    @if ($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                <td>
                    {{ $laporan->progres_keuangan }}%
                    @if($laporan->dokumen_keuangan)
                        <a href="{{ asset('storage/' . $laporan->dokumen_keuangan) }}" target="_blank" title="Lihat Dokumen Keuangan" style="text-decoration: none; font-size: 1.2em;">📄</a>
                    @endif
                </td>
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
                    @if($laporan->validator)
                         <div style="font-size: 10px; color: #666; margin-top: 2px;">
                             Oleh: {{ $laporan->validator->nama }}
                         </div>
                    @endif
                </td>

                <td>
                    <button class="btn btn-outline" 
                            onclick="showDetailLaporan(this)" 
                            data-json="{{ $laporan->load('foto', 'validator')->toJson() }}">
                        Detail
                    </button>

                    @if(in_array(Auth::user()->role->nama_role, ['Admin', 'Pimpinan']) && $laporan->status_validasi == 'Menunggu Validasi')
                        <div style="margin-top: 5px; display: flex; gap: 3px;">
                            
                            <!-- PERBAIKAN: Tombol Setujui memanggil Modal Konfirmasi (bukan alert) -->
                            <button type="button" class="btn btn-success" style="padding: 2px 5px; font-size: 10px;" title="Setujui" onclick="showApproveModal({{ $laporan->id }})">✔</button>
                            
                            <!-- Tombol Tolak memanggil Modal Alasan -->
                            <button type="button" class="btn btn-danger" style="padding: 2px 5px; font-size: 10px;" title="Tolak" onclick="showRejectModal({{ $laporan->id }})">✖</button>
                        
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

<!-- ========================================== -->
<!-- MODAL DETAIL LAPORAN -->
<!-- ========================================== -->
<div id="detailLaporanModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Detail Laporan Progres</h3>
            <button class="close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="modal-body">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <div>
                    <label style="color: #7f8c8d; font-size: 12px;">Tanggal Laporan</label>
                    <div id="modalTanggal" style="font-weight: bold;">-</div>
                </div>
                <div>
                    <label style="color: #7f8c8d; font-size: 12px;">Status Validasi</label>
                    <div id="modalStatus">-</div>
                    <small id="modalValidator" style="color: #666;"></small>
                </div>
                <div>
                    <label style="color: #7f8c8d; font-size: 12px;">Progres Fisik</label>
                    <div id="modalFisik" style="font-weight: bold;">-</div>
                </div>
                <div>
                    <label style="color: #7f8c8d; font-size: 12px;">Progres Keuangan</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div id="modalKeuangan" style="font-weight: bold;">-</div>
                        <a id="modalDokumenLink" href="#" target="_blank" style="font-size: 11px; display: none; color: #3498db; text-decoration: underline;">[Lihat Dokumen]</a>
                    </div>
                </div>
            </div>

            <div id="modalAlasanBox" style="margin-bottom: 20px; display: none;">
                <label style="color: #e74c3c; font-size: 12px; font-weight: bold;">Alasan Penolakan:</label>
                <div id="modalAlasan" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; border: 1px solid #f5c6cb;"></div>
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

<!-- ========================================== -->
<!-- MODAL KONFIRMASI TERIMA (BARU) -->
<!-- ========================================== -->
<div id="approveModal" class="modal">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <div class="modal-header" style="justify-content: center; border-bottom: none; padding-bottom: 0;">
            <h3 style="color: #2ecc71; margin: 0;">Konfirmasi Penyetujuan</h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menyetujui laporan ini?</p>
            <p style="color: #666; font-size: 0.9rem;">Status laporan akan berubah menjadi <strong>Disetujui</strong>.</p>
        </div>
        <div class="modal-footer" style="justify-content: center; border-top: none; padding-top: 0;">
            <button class="btn btn-outline" onclick="closeApproveModal()">Batal</button>
            <form id="approveForm" method="POST" action="">
                @csrf
                <input type="hidden" name="status" value="Disetujui">
                <button type="submit" class="btn btn-success">Ya, Setujui</button>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KONFIRMASI TOLAK (SUDAH ADA) -->
<!-- ========================================== -->
<div id="rejectModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 style="color: #e74c3c;">Tolak Laporan</h3>
            <button class="close" onclick="closeRejectModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="rejectForm" method="POST" action="">
                @csrf
                <input type="hidden" name="status" value="Ditolak">
                
                <div class="form-group">
                    <label style="font-weight: bold; margin-bottom: 8px; display: block;">Alasan Penolakan <span style="color:red">*</span></label>
                    <textarea name="alasan_penolakan" class="form-control" rows="4" required placeholder="Jelaskan mengapa laporan ini ditolak..." style="width: 100%; padding: 10px;"></textarea>
                </div>

                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" class="btn btn-outline" onclick="closeRejectModal()">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const storageBaseUrl = "{{ asset('storage') }}"; 
    
    // --- 1. LOGIKA MODAL DETAIL LAPORAN ---
    function showDetailLaporan(button) {
        const data = JSON.parse(button.getAttribute('data-json'));
        document.getElementById('modalTanggal').innerText = new Date(data.tanggal_laporan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('modalFisik').innerText = data.progres_fisik + '%';
        document.getElementById('modalKeuangan').innerText = data.progres_keuangan + '%';
        document.getElementById('modalKeterangan').innerText = data.keterangan;
        
        // Link Dokumen
        const docLink = document.getElementById('modalDokumenLink');
        if (data.dokumen_keuangan) {
            docLink.href = `${storageBaseUrl}/${data.dokumen_keuangan}`;
            docLink.style.display = 'inline';
        } else {
            docLink.style.display = 'none';
        }

        const statusEl = document.getElementById('modalStatus');
        const alasanBox = document.getElementById('modalAlasanBox');
        
        statusEl.innerText = data.status_validasi;
        statusEl.className = ''; 
        
        if(data.status_validasi == 'Disetujui') {
            statusEl.classList.add('status', 'selesai');
            alasanBox.style.display = 'none';
        } else if(data.status_validasi == 'Ditolak') {
            statusEl.classList.add('status', 'terlambat');
            if(data.alasan_penolakan) {
                document.getElementById('modalAlasan').innerText = data.alasan_penolakan;
                alasanBox.style.display = 'block';
            } else {
                alasanBox.style.display = 'none';
            }
        } else {
            statusEl.classList.add('status', 'berjalan');
            alasanBox.style.display = 'none';
        }

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

    function closeDetailModal() { 
        document.getElementById('detailLaporanModal').style.display = 'none'; 
    }

    // --- 2. LOGIKA MODAL TERIMA (APPROVE) ---
    const approveModal = document.getElementById('approveModal');
    const approveForm = document.getElementById('approveForm');

    function showApproveModal(laporanId) {
        approveForm.action = `/laporan/${laporanId}/validasi`;
        approveModal.style.display = 'flex';
    }

    function closeApproveModal() {
        approveModal.style.display = 'none';
    }

    // --- 3. LOGIKA MODAL TOLAK (REJECT) ---
    const rejectModal = document.getElementById('rejectModal');
    const rejectForm = document.getElementById('rejectForm');

    function showRejectModal(laporanId) {
        rejectForm.action = `/laporan/${laporanId}/validasi`;
        rejectModal.style.display = 'flex';
    }

    function closeRejectModal() {
        rejectModal.style.display = 'none';
    }

    // --- 4. GLOBAL CLICK LISTENER ---
    window.onclick = function(event) { 
        if (event.target == document.getElementById('detailLaporanModal')) closeDetailModal(); 
        if (event.target == approveModal) closeApproveModal();
        if (event.target == rejectModal) closeRejectModal();
    }
</script>
@endsection