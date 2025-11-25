@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="page-header">
    <h2>Kelola Pengguna</h2>
    <ul class="breadcrumb">
        <li>Home</li>
        <li>Kelola Pengguna</li>
    </ul>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Daftar Pengguna Sistem</h3>
        <div class="table-actions">
            <button class="btn btn-primary" onclick="showAddModal()">
                + Tambah Pengguna Baru
            </button>
        </div>
    </div>
    
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
            @foreach($users as $user)
            <tr>
                <td>{{ $user->nama }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->nama_role }}</td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn btn-outline" onclick="editPengguna({{ $user->id }})">Edit</button>
                        
                        <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

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
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" required style="width:100%; padding:8px;">
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control" required style="width:100%; padding:8px;">
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Role</label>
                    <select name="role_id" id="role_id" class="form-control" required style="width:100%; padding:8px;">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Password <small id="passHint" style="color:red; display:none;">(Kosongkan jika tidak ingin mengganti password)</small></label>
                    <input type="password" name="password" id="password" class="form-control" style="width:100%; padding:8px;">
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
    const modal = document.getElementById('penggunaModal');
    const form = document.getElementById('penggunaForm');
    const title = document.getElementById('modalTitle');
    const methodInput = document.getElementById('formMethod');
    const passHint = document.getElementById('passHint');
    const passwordInput = document.getElementById('password');

    function showAddModal() {
        // Reset Form untuk mode Tambah
        form.reset();
        form.action = "{{ route('pengguna.store') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Pengguna Baru";
        
        // Password Wajib saat tambah
        passwordInput.required = true;
        passHint.style.display = 'none';
        
        modal.style.display = 'flex';
    }

    function editPengguna(id) {
        // Fetch data pengguna dari server
        fetch(`/kelola-pengguna/${id}`)
            .then(response => response.json())
            .then(data => {
                // Isi form dengan data yang diambil
                document.getElementById('nama').value = data.nama;
                document.getElementById('email').value = data.email;
                document.getElementById('role_id').value = data.role_id;
                
                // Ubah Form ke mode Edit
                form.action = `/kelola-pengguna/${id}`;
                methodInput.value = "PUT"; // Ubah method jadi PUT
                title.innerText = "Edit Data Pengguna";
                
                // Password Opsional saat edit
                passwordInput.value = ""; // Kosongkan field password
                passwordInput.required = false; 
                passHint.style.display = 'inline';
                
                modal.style.display = 'flex';
            })
            .catch(error => alert('Gagal mengambil data pengguna'));
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection