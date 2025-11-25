@extends('layouts.app')

@section('title', 'Kelola Sekolah')

@section('content')
<style>
    /* Style tambahan */
    .school-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
    .stat-card .number { font-size: 2.5rem; font-weight: bold; color: #2c3e50; margin-bottom: 0.5rem; }
    .stat-card .label { color: #7f8c8d; font-size: 0.9rem; }

    .action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.8rem; }
    
    /* Modal styles */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 8px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; display: flex; flex-direction: column;}
    .modal-header { padding: 1.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 1rem; }
    
    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .detail-item label { font-weight: 500; color: #7f8c8d; display: block; margin-bottom: 0.25rem; }
    .project-item { padding: 0.75rem; border: 1px solid #eee; border-radius: 4px; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; }
</style>

<div class="page-header">
    <h2>Kelola Sekolah</h2>
    <ul class="breadcrumb">
        <li>SIP-RPS</li>
        <li>Kelola Sekolah</li>
    </ul>
</div>

<!-- Statistik -->
<div class="school-stats">
    <div class="stat-card">
        <div class="number">{{ $totalSekolah }}</div>
        <div class="label">Total Sekolah</div>
    </div>
    <div class="stat-card">
        <div class="number">{{ $sekolahProyekAktif }}</div>
        <div class="label">Sekolah dengan Proyek Aktif</div>
    </div>
    <div class="stat-card">
        <div class="number">{{ $sekolahKota }}</div>
        <div class="label">Sekolah di Kota</div>
    </div>
    <div class="stat-card">
        <div class="number">{{ $sekolahKab }}</div>
        <div class="label">Sekolah di Kabupaten</div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Daftar Sekolah</h3>
        <div class="table-actions">
            <!-- Form Filter Server Side -->
            <form method="GET" action="{{ route('sekolah.index') }}" style="display:flex; gap:10px;">
                <div class="search-box">
                    <input type="text" name="search" placeholder="Cari sekolah..." value="{{ request('search') }}">
                </div>
                <select name="kabupaten" class="form-control" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Kabupaten/Kota</option>
                    @foreach(['Banjarmasin', 'Banjarbaru', 'Martapura', 'Barabai', 'Kandangan', 'Rantau', 'Amuntai', 'Kota Baru', 'Batulicin', 'Paringin'] as $kab)
                        <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>{{ $kab }}</option>
                    @endforeach
                </select>
            </form>
            
            <button class="btn btn-primary" onclick="showAddSchoolModal()">
                + Tambah Sekolah Baru
            </button>
        </div>
    </div>
    
    <!-- Error Server Side -->
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
                <th>Nama Sekolah</th>
                <th>Alamat</th>
                <th>Kabupaten/Kota</th>
                <th>Jumlah Proyek</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sekolahs as $sekolah)
            <tr>
                <td>{{ $sekolah->nama_sekolah }}</td>
                <td>{{ Str::limit($sekolah->alamat, 30) }}</td>
                <td>{{ $sekolah->kabupaten_kota }}</td>
                <td>{{ $sekolah->proyek_count }}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-outline btn-sm" onclick="viewSchoolDetails({{ $sekolah->id }})">Detail</button>
                        <button class="btn btn-outline btn-sm" onclick="editSchool({{ $sekolah->id }})">Edit</button>
                        
                        <form action="{{ route('sekolah.destroy', $sekolah->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus sekolah ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;">Tidak ada data sekolah ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Pagination Laravel -->
    <div style="margin-top: 20px;">
        {{ $sekolahs->withQueryString()->links() }}
    </div>
</div>

<!-- Modal Tambah/Edit Sekolah -->
<div id="schoolModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Sekolah Baru</h3>
            <button class="close" onclick="closeSchoolModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Form mengarah ke Route Laravel -->
            <form id="schoolForm" method="POST" action="{{ route('sekolah.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="schoolId" name="id">
                
                <!-- Nama Sekolah -->
                <div class="form-group">
                    <label>Nama Sekolah <span style="color:red">*</span></label>
                    <input type="text" name="nama_sekolah" id="namaSekolah" class="form-control" required>
                    <small id="error_namaSekolah" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Nama sekolah tidak boleh kosong</small>
                </div>
                
                <!-- Jenjang -->
                <div class="form-group">
                    <label>Jenjang Pendidikan <span style="color:red">*</span></label>
                    <select name="jenjang" id="jenjangSekolah" class="form-control" required>
                        <option value="">Pilih Jenjang</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="SMK">SMK</option>
                        <option value="SLB">SLB</option>
                    </select>
                    <small id="error_jenjangSekolah" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Pilih jenjang pendidikan</small>
                </div>
                
                <!-- Alamat -->
                <div class="form-group">
                    <label>Alamat Lengkap <span style="color:red">*</span></label>
                    <textarea name="alamat" id="alamatSekolah" class="form-control" rows="3" required></textarea>
                    <small id="error_alamatSekolah" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Alamat tidak boleh kosong</small>
                </div>
                
                <!-- Kabupaten -->
                <div class="form-group">
                    <label>Kabupaten/Kota <span style="color:red">*</span></label>
                    <select name="kabupaten_kota" id="kabupatenSekolah" class="form-control" required>
                        <option value="">Pilih Kabupaten</option>
                        @foreach(['Banjarmasin', 'Banjarbaru', 'Martapura', 'Barabai', 'Kandangan', 'Rantau', 'Amuntai', 'Kota Baru', 'Batulicin', 'Paringin'] as $kab)
                            <option value="{{ $kab }}">{{ $kab }}</option>
                        @endforeach
                    </select>
                    <small id="error_kabupatenSekolah" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Pilih kabupaten/kota</small>
                </div>

                <div class="form-group">
                    <label>Kecamatan</label>
                    <input type="text" name="kecamatan" id="kecamatanSekolah" class="form-control">
                </div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <!-- Telepon -->
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="telepon" id="teleponSekolah" class="form-control" placeholder="Contoh: 08123456789">
                        <small id="error_teleponSekolah" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Telepon hanya boleh berisi angka</small>
                    </div>
                    <!-- Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="emailSekolah" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Kepala Sekolah</label>
                    <input type="text" name="kepala_sekolah" id="kepalaSekolah" class="form-control">
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <!-- Jumlah Siswa -->
                    <div class="form-group">
                        <label>Jumlah Siswa</label>
                        <input type="number" name="jumlah_siswa" id="jumlahSiswa" class="form-control" min="0">
                        <small id="error_jumlahSiswa" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Jumlah siswa tidak boleh negatif</small>
                    </div>
                    <div class="form-group">
                        <label>Kondisi</label>
                        <select name="kondisi_sekolah" id="kondisiSekolah" class="form-control">
                            <option value="Baik">Baik</option>
                            <option value="Cukup">Cukup</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>
                
                <div style="text-align:right; margin-top:20px;">
                    <button type="button" class="btn btn-outline" onclick="closeSchoolModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Sekolah -->
<div id="detailModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 id="detailTitle">Detail Sekolah</h3>
            <button class="close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="school-details">
                <h4 id="detailNamaSekolah">Loading...</h4>
                
                <div class="detail-grid">
                    <div class="detail-item"><label>Jenjang</label><span id="detailJenjang">-</span></div>
                    <div class="detail-item"><label>Alamat</label><span id="detailAlamat">-</span></div>
                    <div class="detail-item"><label>Kab/Kota</label><span id="detailKabupaten">-</span></div>
                    <div class="detail-item"><label>Kecamatan</label><span id="detailKecamatan">-</span></div>
                    <div class="detail-item"><label>Kepala Sek.</label><span id="detailKepala">-</span></div>
                    <div class="detail-item"><label>Siswa</label><span id="detailSiswa">-</span></div>
                    <div class="detail-item"><label>Kondisi</label><span id="detailKondisi">-</span></div>
                </div>
            </div>
            
            <div class="projects-list">
                <h4>Proyek Rehabilitasi di Sekolah Ini</h4>
                <div id="projectListContainer">
                    <!-- List Proyek akan di-load via JS -->
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    // --- LOGIKA VALIDASI ---
    const submitBtn = document.getElementById('submitBtn');
    const requiredFields = ['namaSekolah', 'jenjangSekolah', 'alamatSekolah', 'kabupatenSekolah'];
    
    const inputTelepon = document.getElementById('teleponSekolah');
    const inputSiswa = document.getElementById('jumlahSiswa');

    function validateSchoolForm() {
        let isValid = true;

        // 1. Cek Field Wajib (Nama, Jenjang, Alamat, Kab)
        requiredFields.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            const errorMsg = document.getElementById('error_' + fieldId);
            
            if (!input.value.trim()) {
                errorMsg.style.display = 'block';
                isValid = false;
            } else {
                errorMsg.style.display = 'none';
            }
        });

        // 2. Cek Telepon (Hanya Angka)
        const telVal = inputTelepon.value.trim();
        const errorTel = document.getElementById('error_teleponSekolah');
        // Regex: hanya angka 0-9
        if (telVal && !/^\d+$/.test(telVal)) {
            errorTel.style.display = 'block';
            isValid = false;
        } else {
            errorTel.style.display = 'none';
        }

        // 3. Cek Jumlah Siswa (Tidak Negatif)
        const siswaVal = inputSiswa.value;
        const errorSiswa = document.getElementById('error_jumlahSiswa');
        if (siswaVal && siswaVal < 0) {
            errorSiswa.style.display = 'block';
            isValid = false;
        } else {
            errorSiswa.style.display = 'none';
        }

        // Update Tombol
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
    requiredFields.forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('input', validateSchoolForm);
        el.addEventListener('change', validateSchoolForm);
    });
    inputTelepon.addEventListener('input', validateSchoolForm);
    inputSiswa.addEventListener('input', validateSchoolForm);


    // --- MODAL & AJAX LOGIC ---
    
    function showAddSchoolModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Sekolah Baru';
        const form = document.getElementById('schoolForm');
        form.reset();
        form.action = "{{ route('sekolah.store') }}"; // Reset ke route store
        document.getElementById('formMethod').value = "POST";
        
        // Reset Validasi Visual
        document.querySelectorAll('small[id^="error_"]').forEach(e => e.style.display = 'none');
        validateSchoolForm(); // Cek awal

        document.getElementById('schoolModal').style.display = 'flex';
    }

    function closeSchoolModal() {
        document.getElementById('schoolModal').style.display = 'none';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    // Fungsi Fetch Data untuk Edit
    function editSchool(id) {
        // Ambil data dari server
        fetch(`/sekolah/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Edit Data Sekolah';
                
                // Isi form dengan data
                document.getElementById('namaSekolah').value = data.nama_sekolah;
                document.getElementById('jenjangSekolah').value = data.jenjang;
                document.getElementById('alamatSekolah').value = data.alamat;
                document.getElementById('kabupatenSekolah').value = data.kabupaten_kota;
                document.getElementById('kecamatanSekolah').value = data.kecamatan;
                document.getElementById('teleponSekolah').value = data.telepon;
                document.getElementById('emailSekolah').value = data.email;
                document.getElementById('kepalaSekolah').value = data.kepala_sekolah;
                document.getElementById('jumlahSiswa').value = data.jumlah_siswa;
                document.getElementById('kondisiSekolah').value = data.kondisi_sekolah;
                
                // Ubah action form ke update
                const form = document.getElementById('schoolForm');
                form.action = `/sekolah/${id}`;
                document.getElementById('formMethod').value = "PUT"; // Method spoofing Laravel
                
                // Reset Validasi & Cek Ulang
                document.querySelectorAll('small[id^="error_"]').forEach(e => e.style.display = 'none');
                validateSchoolForm();

                document.getElementById('schoolModal').style.display = 'flex';
            })
            .catch(err => alert('Gagal mengambil data sekolah'));
    }

    // Fungsi Fetch Data untuk Detail
    function viewSchoolDetails(id) {
        fetch(`/sekolah/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('detailNamaSekolah').textContent = data.nama_sekolah;
                document.getElementById('detailJenjang').textContent = data.jenjang;
                document.getElementById('detailAlamat').textContent = data.alamat;
                document.getElementById('detailKabupaten').textContent = data.kabupaten_kota;
                document.getElementById('detailKecamatan').textContent = data.kecamatan || '-';
                document.getElementById('detailKepala').textContent = data.kepala_sekolah || '-';
                document.getElementById('detailSiswa').textContent = data.jumlah_siswa + ' Siswa';
                
                const spanKondisi = document.getElementById('detailKondisi');
                spanKondisi.textContent = data.kondisi_sekolah || '-';
                // Reset class dan tambah class dinamis
                spanKondisi.className = ''; 
                if(data.kondisi_sekolah === 'Baik') spanKondisi.classList.add('status', 'berjalan');
                else if(data.kondisi_sekolah === 'Rusak Berat') spanKondisi.classList.add('status', 'terlambat');
                else spanKondisi.classList.add('status', 'selesai');

                // Render List Proyek
                const listContainer = document.getElementById('projectListContainer');
                listContainer.innerHTML = ''; // Kosongkan dulu
                
                if(data.proyek && data.proyek.length > 0) {
                    data.proyek.forEach(p => {
                        // Tentukan class status
                        let statusClass = 'selesai';
                        if(p.status_proyek === 'Berjalan') statusClass = 'berjalan';
                        if(p.status_proyek === 'Terlambat') statusClass = 'terlambat';

                        const html = `
                            <div class="project-item">
                                <div class="project-info">
                                    <h4>${p.nama_proyek}</h4>
                                    <p>Status: <span class="status ${statusClass}">${p.status_proyek}</span> | Anggaran: Rp ${new Intl.NumberFormat('id-ID').format(p.anggaran)}</p>
                                </div>
                            </div>
                        `;
                        listContainer.innerHTML += html;
                    });
                } else {
                    listContainer.innerHTML = '<p style="color:#888; font-style:italic;">Belum ada proyek di sekolah ini.</p>';
                }

                document.getElementById('detailModal').style.display = 'flex';
            });
    }

    // Close Modal ketika klik di luar area konten
    window.onclick = function(event) {
        if (event.target == document.getElementById('schoolModal')) {
            closeSchoolModal();
        }
        if (event.target == document.getElementById('detailModal')) {
            closeDetailModal();
        }
    }
</script>
@endsection