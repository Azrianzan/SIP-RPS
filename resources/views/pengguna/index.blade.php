@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<style>
    /* Style untuk Stat Card */
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
        gap: 1.5rem; 
        margin-bottom: 2rem; 
    }
    .stat-card { 
        background: white; 
        border-radius: 8px; 
        padding: 1.5rem; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        text-align: center; 
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-card .number { 
        font-size: 2.5rem; 
        font-weight: bold; 
        color: #2c3e50; 
        margin-bottom: 0.5rem; 
    }
    .stat-card .label { 
        color: #7f8c8d; 
        font-size: 0.9rem; 
        font-weight: 500;
    }
    
    /* Warna border khusus */
    .stat-card.admin { border-bottom: 4px solid #3498db; }
    .stat-card.pimpinan { border-bottom: 4px solid #2ecc71; }
    .stat-card.pjl { border-bottom: 4px solid #f39c12; }
    .stat-card.total { border-bottom: 4px solid #9b59b6; }
</style>

<div class="page-header">
    <h2>Kelola Pengguna</h2>
    <ul class="breadcrumb">
        <li>SIP-RPS</li>
        <li>Kelola Pengguna</li>
    </ul>
</div>

<!-- Bagian Statistik Pengguna -->
<div class="stats-grid">
    <div class="stat-card total">
        <div class="number">{{ $totalUser }}</div>
        <div class="label">Total Pengguna</div>
    </div>
    <div class="stat-card admin">
        <div class="number">{{ $totalAdmin }}</div>
        <div class="label">Admin</div>
    </div>
    <div class="stat-card pimpinan">
        <div class="number">{{ $totalPimpinan }}</div>
        <div class="label">Pimpinan</div>
    </div>
    <div class="stat-card pjl">
        <div class="number">{{ $totalPjl }}</div>
        <div class="label">PJL</div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Daftar Pengguna Sistem</h3>
        <div class="table-actions">
            <!-- Form Pencarian -->
            <form action="{{ route('pengguna.index') }}" method="GET" class="search-box">
                <input type="text" name="search" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                <!-- Tombol submit hidden agar bisa di-enter -->
                <button type="submit" style="display: none;"></button>
            </form>

            <button class="btn btn-primary" onclick="showAddModal()">
                + Tambah Pengguna Baru
            </button>
        </div>
    </div>
    
    <!-- Menampilkan Error dari Server (Laravel) -->
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
                <th>Nama Pengguna</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->nama }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->nama_role }}</td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn btn-outline" onclick="editPengguna({{ $user->id }})">Edit</button>
                        
                        <!-- PERUBAHAN: Tombol Hapus memanggil fungsi JS, bukan submit form langsung -->
                        <button class="btn btn-danger" onclick="confirmDelete({{ $user->id }}, '{{ $user->nama }}')">Hapus</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #888;">Tidak ada pengguna yang ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- MODAL TAMBAH / EDIT PENGGUNA -->
<div id="penggunaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Pengguna Baru</h3>
            <button class="close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="penggunaForm" method="POST" action="{{ route('pengguna.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <!-- Input Nama -->
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Nama <span style="color:red">*</span></label>
                    <input type="text" name="nama" id="nama" class="form-control" maxlength="255" required style="width:100%; padding:8px;">
                    <small id="error_nama" style="color: red; display: none; font-size: 12px; margin-top:5px;">Nama tidak boleh kosong</small>
                    <small id="error_nama_regex" style="color: red; display: none; font-size: 12px; margin-top:5px;">Nama hanya boleh berisi huruf, spasi, dan titik.</small>
                </div>
                
                <!-- Input Email -->
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Email <span style="color:red">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" maxlength="255" required style="width:100%; padding:8px;">
                    <small id="error_email" style="color: red; display: none; font-size: 12px; margin-top:5px;">Email tidak boleh kosong</small>
                    <small id="error_email_format" style="color: red; display: none; font-size: 12px; margin-top:5px;">Format email tidak valid (contoh: user@email.com)</small>
                    <small id="error_email_duplicate" style="color: red; display: none; font-size: 12px; margin-top:5px;">Email ini sudah digunakan oleh pengguna lain</small>
                </div>
                
                <!-- Input Role -->
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Role <span style="color:red">*</span></label>
                    <select name="role_id" id="role_id" class="form-control" required style="width:100%; padding:8px;">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                        @endforeach
                    </select>
                    <small id="error_role_id" style="color: red; display: none; font-size: 12px; margin-top:5px;">Role tidak boleh kosong</small>
                </div>

                <!-- Input Password -->
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Password <span id="reqPass" style="color:red">*</span> <small id="passHint" style="color:#666; font-weight:normal; display:none;">(Kosongkan jika tidak ingin mengganti)</small></label>
                    <input type="password" name="password" id="password" class="form-control" maxlength="255" style="width:100%; padding:8px;">
                    <small id="error_password" style="color: red; display: none; font-size: 12px; margin-top:5px;">Password wajib diisi untuk pengguna baru</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled style="opacity: 0.5; cursor: not-allowed;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS (BARU) -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <div class="modal-header" style="justify-content: center; border-bottom: none; padding-bottom: 0;">
            <h3 style="color: #e74c3c; margin: 0;">Konfirmasi Hapus</h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus pengguna <br><strong id="deleteUserName"></strong>?</p>
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
    const modal = document.getElementById('penggunaModal');
    const deleteModal = document.getElementById('deleteModal');
    const form = document.getElementById('penggunaForm');
    const deleteForm = document.getElementById('deleteForm');
    const title = document.getElementById('modalTitle');
    const methodInput = document.getElementById('formMethod');
    const passHint = document.getElementById('passHint');
    const reqPass = document.getElementById('reqPass');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');

    // Ambil daftar email yang sudah ada dari PHP ke JS untuk validasi unik
    const existingEmails = @json($users->pluck('email'));
    
    // Variabel State
    let isEditMode = false;
    let currentEditingEmail = '';

    // --- LOGIKA VALIDASI ---
    const inputs = ['nama', 'email', 'role_id', 'password'];

    // Regex untuk Nama: Hanya huruf, spasi, dan titik.
    const nameRegex = /^[a-zA-Z\s\.]+$/;
    // Regex untuk Email: Format standar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function validateForm() {
        let isValid = true;

        // 1. Validasi Nama
        const nama = document.getElementById('nama');
        const namaVal = nama.value.trim();
        const errNama = document.getElementById('error_nama');
        const errNamaRegex = document.getElementById('error_nama_regex');

        if (!namaVal) {
            errNama.style.display = 'block';
            errNamaRegex.style.display = 'none';
            isValid = false;
        } else if (!nameRegex.test(namaVal)) {
            errNama.style.display = 'none';
            errNamaRegex.style.display = 'block';
            isValid = false;
        } else {
            errNama.style.display = 'none';
            errNamaRegex.style.display = 'none';
        }

        // 2. Validasi Email (Kosong, Format, & Unik)
        const email = document.getElementById('email');
        const emailVal = email.value.trim();
        const errEmail = document.getElementById('error_email');
        const errEmailFormat = document.getElementById('error_email_format');
        const errDuplicate = document.getElementById('error_email_duplicate');

        if (!emailVal) {
            errEmail.style.display = 'block';
            errEmailFormat.style.display = 'none';
            errDuplicate.style.display = 'none';
            isValid = false;
        } else if (!emailRegex.test(emailVal)) {
            errEmail.style.display = 'none';
            errEmailFormat.style.display = 'block';
            errDuplicate.style.display = 'none';
            isValid = false;
        } else {
            errEmail.style.display = 'none';
            errEmailFormat.style.display = 'none';
            
            // Cek Unik
            if (existingEmails.includes(emailVal) && emailVal !== currentEditingEmail) {
                errDuplicate.style.display = 'block';
                isValid = false;
            } else {
                errDuplicate.style.display = 'none';
            }
        }

        // 3. Validasi Role
        const role = document.getElementById('role_id');
        if (!role.value) {
            document.getElementById('error_role_id').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('error_role_id').style.display = 'none';
        }

        // 4. Validasi Password
        const pass = document.getElementById('password');
        // Jika Mode Tambah: Password Wajib
        if (!isEditMode && !pass.value.trim()) {
            document.getElementById('error_password').style.display = 'block';
            isValid = false;
        } else {
            document.getElementById('error_password').style.display = 'none';
        }

        // Update Tombol Submit
        submitBtn.disabled = !isValid;
        if (!isValid) {
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    }

    // Pasang Event Listener
    inputs.forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('input', validateForm);
        el.addEventListener('change', validateForm);
    });

    // --- LOGIKA MODAL TAMBAH/EDIT ---

    function showAddModal() {
        isEditMode = false;
        currentEditingEmail = ''; 

        form.reset();
        form.action = "{{ route('pengguna.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Pengguna Baru";
        
        reqPass.style.display = 'inline'; 
        passHint.style.display = 'none';
        
        document.querySelectorAll('small[id^="error_"]').forEach(el => el.style.display = 'none');
        
        validateForm(); 
        modal.style.display = 'flex';
    }

    function editPengguna(id) {
        isEditMode = true;

        fetch(`/kelola-pengguna/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('nama').value = data.nama;
                document.getElementById('email').value = data.email;
                document.getElementById('role_id').value = data.role_id;
                
                currentEditingEmail = data.email; 

                form.action = `/kelola-pengguna/${id}`;
                methodInput.value = "PUT"; 
                title.innerText = "Edit Data Pengguna";
                
                passwordInput.value = ""; 
                reqPass.style.display = 'none'; 
                passHint.style.display = 'inline'; 
                
                document.querySelectorAll('small[id^="error_"]').forEach(el => el.style.display = 'none');
                validateForm(); 
                
                modal.style.display = 'flex';
            })
            .catch(error => alert('Gagal mengambil data pengguna'));
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    // --- LOGIKA MODAL HAPUS ---

    function confirmDelete(id, name) {
        document.getElementById('deleteUserName').innerText = name;
        deleteForm.action = `/kelola-pengguna/${id}`;
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
</script>
@endsection