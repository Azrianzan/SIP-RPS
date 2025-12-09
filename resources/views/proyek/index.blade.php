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
            
            <!-- Form Pencarian -->
            <form action="{{ route('proyek.index') }}" method="GET" class="search-box">
                <input type="text" name="search" placeholder="Cari proyek atau sekolah..." value="{{ request('search') }}">
                <!-- Tombol submit hidden agar form bisa disubmit dengan Enter -->
                <button type="submit" style="display: none;"></button>
            </form>
            
            <!-- Tombol Tambah (Hanya Admin) -->
            @if(Auth::user()->role->nama_role === 'Admin')
                <button class="btn btn-primary" onclick="showAddModal()">
                    + Tambah Proyek Baru
                </button>
            @endif
            
        </div>
    </div>
    
    <!-- Alert Error PHP -->
    @if ($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabel Data -->
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
                    <a href="{{ route('proyek.show', $proyek->id) }}" class="btn btn-outline">Detail</a>
                    
                    @if(Auth::user()->role->nama_role === 'Admin')
                        <button class="btn btn-outline" onclick="editProyek({{ $proyek->id }})">Edit</button>
                        
                        <!-- Tombol Hapus memicu Modal -->
                        <button class="btn btn-danger" onclick="confirmDelete({{ $proyek->id }}, '{{ $proyek->nama_proyek }}')">Hapus</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- MODAL TAMBAH / EDIT PROYEK -->
<!-- ========================================== -->
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
                
                <!-- Input Nama Proyek -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Nama Proyek <span style="color:red">*</span></label>
                    <input type="text" name="nama_proyek" id="nama_proyek" class="form-control" maxlength="255" required style="width:100%; padding:8px;">
                    <small id="error_nama_proyek" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Nama proyek tidak boleh kosong</small>
                    <small id="error_nama_proyek_regex" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Nama proyek hanya boleh huruf, angka, dan spasi (tanpa simbol)</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <!-- Input Sekolah -->
                    <div class="form-group">
                        <label>Sekolah <span style="color:red">*</span></label>
                        <select name="sekolah_id" id="sekolah_id" class="form-control" required style="width:100%; padding:8px;">
                            <option value="">Pilih Sekolah</option>
                            @foreach($sekolahs as $sekolah)
                                <option value="{{ $sekolah->id }}">{{ $sekolah->nama_sekolah }}</option>
                            @endforeach
                        </select>
                        <small id="error_sekolah_id" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Sekolah tidak boleh kosong</small>
                    </div>
                    
                    <!-- Input Anggaran -->
                    <div class="form-group">
                        <label>Anggaran (Rp) <span style="color:red">*</span></label>
                        <!-- Maxlength tidak berlaku di type number, gunakan max value -->
                        <input type="number" name="anggaran" id="anggaran" class="form-control" min="0" max="999999999999999" required style="width:100%; padding:8px;">
                        <small id="error_anggaran" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Anggaran tidak boleh kosong</small>
                        <small id="anggaranNegativeError" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Anggaran tidak boleh negatif</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <!-- Input Tanggal Mulai -->
                    <div class="form-group">
                        <label>Tanggal Mulai <span style="color:red">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required style="width:100%; padding:8px;">
                        <small id="error_tanggal_mulai" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Tanggal mulai tidak boleh kosong</small>
                    </div>
                    
                    <!-- Input Tanggal Selesai -->
                    <div class="form-group">
                        <label>Tanggal Selesai <span style="color:red">*</span></label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required style="width:100%; padding:8px;">
                        <small id="error_tanggal_selesai" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Tanggal selesai tidak boleh kosong</small>
                        <small id="dateLogicError" style="color: red; display: none; font-size: 12px; margin-top:5px;">Tanggal selesai harus lebih akhir dari tanggal mulai</small>
                    </div>
                </div>

                <!-- Input PJL -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Penanggung Jawab Lapangan (PJL) <span style="color:red">*</span></label>
                    <select name="pjl_id" id="pjl_id" class="form-control" required style="width:100%; padding:8px;">
                        <option value="">Pilih PJL</option>
                        @foreach($pjls as $pjl)
                            <option value="{{ $pjl->id }}">{{ $pjl->nama }}</option>
                        @endforeach
                    </select>
                    <small id="error_pjl_id" style="color: red; display: none; font-size: 12px; margin-top: 5px;">PJL tidak boleh kosong</small>
                </div>

                <!-- Input Status (Hanya muncul saat Edit) -->
                <div class="form-group" id="statusGroup" style="margin-bottom: 15px; display: none;">
                    <label>Status Proyek</label>
                    <select name="status_proyek" id="status_proyek" class="form-control" style="width:100%; padding:8px;">
                        <option value="Persiapan">Persiapan</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>

                <!-- Input Deskripsi -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" maxlength="1000" style="width:100%; padding:8px;"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                    <!-- ID submitBtn digunakan untuk disable jika tidak valid -->
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled style="opacity: 0.5; cursor: not-allowed;">Simpan Proyek</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KONFIRMASI HAPUS (BARU) -->
<!-- ========================================== -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <div class="modal-header" style="justify-content: center; border-bottom: none; padding-bottom: 0;">
            <h3 style="color: #e74c3c; margin: 0;">Konfirmasi Hapus</h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus proyek <br><strong id="deleteProjectName"></strong>?</p>
            <p style="color: #666; font-size: 0.9rem;">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer" style="justify-content: center; border-top: none; padding-top: 0;">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Batal</button>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
    // --- SETUP ELEMENT ---
    const modal = document.getElementById('proyekModal');
    const deleteModal = document.getElementById('deleteModal');
    const form = document.getElementById('proyekForm');
    const deleteForm = document.getElementById('deleteForm');
    const methodInput = document.getElementById('formMethod');
    const title = document.getElementById('modalTitle');
    const statusGroup = document.getElementById('statusGroup');
    const submitBtn = document.getElementById('submitBtn');

    // Daftar ID input yang wajib diisi
    const requiredFields = ['nama_proyek', 'sekolah_id', 'anggaran', 'tanggal_mulai', 'tanggal_selesai', 'pjl_id'];

    // Regex Nama Proyek: Hanya huruf, angka, dan spasi (Tanpa simbol aneh)
    const nameRegex = /^[a-zA-Z0-9\s]+$/;

    // Fungsi Validasi Menyeluruh
    function validateForm() {
        let isValid = true;

        // 1. Validasi Nama Proyek (Kosong & Regex)
        const namaInput = document.getElementById('nama_proyek');
        const namaVal = namaInput.value.trim();
        if (!namaVal) {
            document.getElementById('error_nama_proyek').style.display = 'block';
            document.getElementById('error_nama_proyek_regex').style.display = 'none';
            isValid = false;
        } else if (!nameRegex.test(namaVal)) {
            document.getElementById('error_nama_proyek').style.display = 'none';
            document.getElementById('error_nama_proyek_regex').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('error_nama_proyek').style.display = 'none';
            document.getElementById('error_nama_proyek_regex').style.display = 'none';
        }

        // 2. Validasi Field Wajib Lainnya (Selain Nama)
        requiredFields.forEach(fieldId => {
            if(fieldId === 'nama_proyek') return; // Skip nama karena sudah dicek di atas

            const input = document.getElementById(fieldId);
            const errorText = document.getElementById('error_' + fieldId);
            
            if (!input.value.trim()) {
                if(errorText) errorText.style.display = 'block';
                isValid = false;
            } else {
                if(errorText) errorText.style.display = 'none';
            }
        });

        // 3. Cek Logika Tanggal
        const tglMulai = document.getElementById('tanggal_mulai').value;
        const tglSelesai = document.getElementById('tanggal_selesai').value;
        const dateLogicError = document.getElementById('dateLogicError');

        if (tglMulai && tglSelesai) {
            if (new Date(tglSelesai) < new Date(tglMulai)) {
                dateLogicError.style.display = 'block';
                isValid = false;
            } else {
                dateLogicError.style.display = 'none';
            }
        } else {
            dateLogicError.style.display = 'none';
        }

        // 4. Cek Anggaran Negatif
        const anggaran = document.getElementById('anggaran').value;
        const anggaranNegError = document.getElementById('anggaranNegativeError');
        
        if (anggaran && anggaran < 0) {
            anggaranNegError.style.display = 'block';
            isValid = false;
        } else {
            anggaranNegError.style.display = 'none';
        }

        // Update status tombol Submit
        submitBtn.disabled = !isValid;
        if (!isValid) {
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    }

    // Pasang Event Listener ke semua input
    requiredFields.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        input.addEventListener('input', validateForm);
        input.addEventListener('change', validateForm);
    });

    // --- LOGIKA MODAL TAMBAH/EDIT ---

    function showAddModal() {
        form.reset();
        form.action = "{{ route('proyek.store') }}"; 
        methodInput.value = "POST";
        title.innerText = "Tambah Proyek Baru";
        statusGroup.style.display = 'none'; 
        
        // Reset pesan error
        document.querySelectorAll('small[id^="error_"]').forEach(el => el.style.display = 'none');
        document.getElementById('dateLogicError').style.display = 'none';
        document.getElementById('anggaranNegativeError').style.display = 'none';
        
        // Cek validasi awal (tombol akan disabled karena form kosong)
        validateForm(); 

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
                
                // Reset pesan error tampilan
                document.querySelectorAll('small[id^="error_"]').forEach(el => el.style.display = 'none');
                document.getElementById('dateLogicError').style.display = 'none';
                document.getElementById('anggaranNegativeError').style.display = 'none';
                
                // Cek validasi (tombol aktif karena data terisi)
                validateForm();

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

    // --- LOGIKA MODAL HAPUS ---

    function confirmDelete(id, name) {
        document.getElementById('deleteProjectName').innerText = name;
        deleteForm.action = `/kelola-proyek/${id}`;
        deleteModal.style.display = 'flex';
    }

    function closeDeleteModal() {
        deleteModal.style.display = 'none';
    }

    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
        if (event.target == deleteModal) {
            closeDeleteModal();
        }
    }

    @if ($errors->any())
        modal.style.display = 'flex';
    @endif
</script>
@endsection