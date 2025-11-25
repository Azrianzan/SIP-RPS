@extends('layouts.app')

@section('title', 'Kelola Proyek')

@section('content')
<div class="page-header">
    <h2>Kelola Proyek</h2>
    <ul class="breadcrumb">
        <li>Home</li>
        <li>Kelola Proyek</li>
    </ul>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Daftar Proyek Rehabilitasi Sekolah</h3>
        <div class="table-actions">
            <div class="search-box">
                <input type="text" placeholder="Cari proyek...">
            </div>
            
            {{-- LOGIKA PERBAIKAN: Tombol Tambah HANYA untuk Admin --}}
            @if(Auth::user()->role->nama_role === 'Admin')
                <button class="btn btn-primary" onclick="showAddModal()">
                    + Tambah Proyek Baru
                </button>
            @endif
            {{-- Akhir Logika --}}
            
        </div>
    </div>
    
    @if ($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nama Proyek</th>
                <th>Sekolah</th>
                <th>Anggaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyeks as $proyek)
            <tr>
                <td>{{ $proyek->nama_proyek }}</td>
                <td>{{ $proyek->sekolah->nama_sekolah ?? 'Data Sekolah Terhapus' }}</td>
                <td>Rp {{ number_format($proyek->anggaran, 0, ',', '.') }}</td>
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
                    <span class="status {{ $statusClass }}">
                        {{ $proyek->status_proyek }}
                    </span>
                </td>
                <td>
                    <!-- Tombol Detail: Bisa diakses SEMUA user (Admin, Pimpinan, PJL) -->
                    <a href="{{ route('proyek.show', $proyek->id) }}" class="btn btn-outline">Detail</a>
                    
                    {{-- LOGIKA PERBAIKAN: Tombol Edit & Hapus HANYA untuk Admin --}}
                    @if(Auth::user()->role->nama_role === 'Admin')
                        <!-- TOMBOL EDIT -->
                        <button class="btn btn-outline" onclick="editProyek({{ $proyek->id }})">Edit</button>
                        
                        <!-- TOMBOL HAPUS -->
                        <form action="{{ route('proyek.destroy', $proyek->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus proyek ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    @endif
                    {{-- Akhir Logika --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- MODAL TAMBAH / EDIT PROYEK -->
<!-- (Hanya dirender/berguna jika Admin, tapi dibiarkan ada agar JS tidak error saat admin login) -->
<div id="proyekModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Proyek Baru</h3>
            <button class="close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('proyek.store') }}" method="POST" id="proyekForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Nama Proyek</label>
                    <input type="text" name="nama_proyek" id="nama_proyek" class="form-control" required style="width:100%; padding:8px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Sekolah</label>
                        <select name="sekolah_id" id="sekolah_id" class="form-control" required style="width:100%; padding:8px;">
                            <option value="">Pilih Sekolah</option>
                            @foreach($sekolahs as $sekolah)
                                <option value="{{ $sekolah->id }}">{{ $sekolah->nama_sekolah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Anggaran (Rp)</label>
                        <input type="number" name="anggaran" id="anggaran" class="form-control" required style="width:100%; padding:8px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required style="width:100%; padding:8px;">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required style="width:100%; padding:8px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Penanggung Jawab Lapangan (PJL)</label>
                    <select name="pjl_id" id="pjl_id" class="form-control" required style="width:100%; padding:8px;">
                        <option value="">Pilih PJL</option>
                        @foreach($pjls as $pjl)
                            <option value="{{ $pjl->id }}">{{ $pjl->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="statusGroup" style="margin-bottom: 15px; display: none;">
                    <label>Status Proyek</label>
                    <select name="status_proyek" id="status_proyek" class="form-control" style="width:100%; padding:8px;">
                        <option value="Persiapan">Persiapan</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" style="width:100%; padding:8px;"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('proyekModal');
    const form = document.getElementById('proyekForm');
    const methodInput = document.getElementById('formMethod');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');

    function showAddModal() {
        form.reset();
        form.action = "{{ route('proyek.store') }}"; 
        methodInput.value = "POST";
        title.innerText = "Tambah Proyek Baru";
        statusGroup.style.display = 'none'; 
        
        modal.style.display = 'flex';
    }

    function editProyek(id) {
        fetch(`/kelola-proyek/${id}/data`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('nama_proyek').value = data.nama_proyek;
                document.getElementById('sekolah_id').value = data.sekolah_id;
                document.getElementById('anggaran').value = Math.floor(data.anggaran); 
                document.getElementById('tanggal_mulai').value = data.tanggal_mulai;
                document.getElementById('tanggal_selesai').value = data.tanggal_selesai;
                document.getElementById('pjl_id').value = data.pjl_id;
                document.getElementById('deskripsi').value = data.deskripsi;
                document.getElementById('status_proyek').value = data.status_proyek;

                form.action = `/kelola-proyek/${id}`; 
                methodInput.value = "PUT"; 
                title.innerText = "Edit Data Proyek";
                statusGroup.style.display = 'block'; 

                modal.style.display = 'flex';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengambil data proyek');
            });
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    @if ($errors->any())
        modal.style.display = 'flex';
    @endif
</script>
@endsection