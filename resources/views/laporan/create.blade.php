@extends('layouts.app')

@section('title', 'Form Laporan Progres')

@section('content')
<div class="page-header">
    <h2>Form Laporan Progres</h2>
    <ul class="breadcrumb">
        <li>SIP-RPS</li>
        <li><a href="{{ route('proyek.show', $proyek->id) }}">Detail Proyek</a></li>
        <li>Form Laporan</li>
    </ul>
</div>

<div class="form-container" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <form action="{{ route('laporan.store', $proyek->id) }}" method="POST" enctype="multipart/form-data" id="laporanForm">
        @csrf
        
        <!-- Informasi Proyek (Readonly) -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="project">Proyek</label>
            <input type="text" class="form-control" value="{{ $proyek->nama_proyek }}" readonly style="background-color: #e9ecef;">
            <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
        </div>
        
        <!-- Tanggal Laporan -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="report-date">Tanggal Laporan <span style="color:red">*</span></label>
            <input type="date" name="tanggal_laporan" id="report-date" class="form-control" required value="{{ date('Y-m-d') }}">
            <small id="error_report-date" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Tanggal laporan tidak boleh kosong</small>
        </div>
        
        <!-- Progres Fisik & Keuangan -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group">
                <label for="physical-progress">Progres Fisik (%) <span style="color:red">*</span></label>
                <input type="number" name="progres_fisik" id="physical-progress" class="form-control" min="0" max="100" placeholder="0-100" required>
                <small id="error_physical-progress" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Wajib diisi (0-100)</small>
            </div>
            
            <div class="form-group">
                <label for="financial-progress">Progres Keuangan (%) <span style="color:red">*</span></label>
                <input type="number" name="progres_keuangan" id="financial-progress" class="form-control" min="0" max="100" placeholder="0-100" required>
                <small id="error_financial-progress" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Wajib diisi (0-100)</small>
            </div>
        </div>
        
        <!-- Nama Pelapor (Readonly) -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="reporter">Nama Penanggung Jawab Lapangan</label>
            <input type="text" class="form-control" value="{{ Auth::user()->nama }}" readonly style="background-color: #e9ecef;">
        </div>
        
        <!-- Judul Laporan -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="report-title">Judul Laporan <span style="color:red">*</span></label>
            <input type="text" name="judul_laporan" id="report-title" class="form-control" placeholder="Contoh: Laporan Minggu ke-3 Pemasangan Atap" maxlength="255" required>
            <small id="error_report-title" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Judul laporan tidak boleh kosong</small>
            <!-- Pesan error diupdate -->
            <small id="error_report-title_regex" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Judul hanya boleh huruf, angka, spasi, koma, dan strip</small>
        </div>
        
        <!-- Deskripsi -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="description">Keterangan / Catatan <span style="color:red">*</span></label>
            <textarea name="keterangan" id="description" class="form-control" rows="4" placeholder="Deskripsi detail kemajuan pekerjaan, kendala yang dihadapi, dan rencana selanjutnya..." required></textarea>
            <small id="error_description" style="color: red; display: none; font-size: 12px; margin-top: 5px;">Keterangan tidak boleh kosong</small>
        </div>
        
        <!-- Upload Foto -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="photos">Unggah Foto Dokumentasi <span style="color:red">*</span></label>
            <input type="file" name="photos[]" id="photos" class="form-control" multiple accept="image/*" required onchange="previewImages()">
            <small style="color: #666;">Unggah foto progres pekerjaan (Bisa pilih banyak sekaligus)</small>
            <small id="error_photos" style="color: red; display: none; font-size: 12px; margin-top: 5px; display: block;">Wajib mengunggah minimal 1 foto</small>
        </div>
        
        <!-- Area Preview Foto -->
        <div id="photo-gallery" class="photo-gallery" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 2rem;"></div>
        
        <!-- Tombol Aksi -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" id="submitBtn" class="btn btn-primary" disabled style="opacity: 0.5; cursor: not-allowed;">Kirim Laporan</button>
            <a href="{{ route('proyek.show', $proyek->id) }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<script>
    // --- 1. PREVIEW GAMBAR ---
    function previewImages() {
        var preview = document.querySelector('#photo-gallery');
        preview.innerHTML = '';
        var files   = document.querySelector('input[type=file]').files;

        function readAndPreview(file) {
            // Validasi tipe gambar sederhana
            if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    var image = new Image();
                    image.height = 100;
                    image.title = file.name;
                    image.src = this.result;
                    image.style.borderRadius = "4px";
                    image.style.border = "1px solid #ddd";
                    preview.appendChild( image );
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
        
        // Trigger validasi ulang saat foto berubah
        validateForm();
    }

    // --- 2. VALIDASI FORM ---
    const submitBtn = document.getElementById('submitBtn');
    
    // Daftar input text/number/date yang wajib
    const inputs = ['report-date', 'physical-progress', 'financial-progress', 'report-title', 'description'];

    // Regex Judul: Huruf, Angka, Spasi, Koma (,), Strip (-) -> Tanpa Titik
    const titleRegex = /^[a-zA-Z0-9\s\,\-]+$/;

    function validateForm() {
        let isValid = true;

        // Cek Input Text/Number/Date
        inputs.forEach(id => {
            const el = document.getElementById(id);
            const errorEl = document.getElementById('error_' + id);
            
            // Cek kosong
            if (!el.value.trim()) {
                if (errorEl) errorEl.style.display = 'block';
                isValid = false;
            } else {
                // Khusus Judul Laporan: Cek Regex Simbol
                if (id === 'report-title') {
                    const regexErrorEl = document.getElementById('error_report-title_regex');
                    
                    if (!titleRegex.test(el.value)) {
                        if (errorEl) errorEl.style.display = 'none'; // Sembunyikan error kosong
                        if (regexErrorEl) regexErrorEl.style.display = 'block'; // Tampilkan error regex
                        isValid = false;
                    } else {
                        if (errorEl) errorEl.style.display = 'none';
                        if (regexErrorEl) regexErrorEl.style.display = 'none';
                    }
                } 
                // Khusus number: cek range 0-100
                else if (el.type === 'number') {
                    const val = parseFloat(el.value);
                    if (val < 0 || val > 100) {
                        if (errorEl) {
                            errorEl.innerText = "Nilai harus antara 0 - 100";
                            errorEl.style.display = 'block';
                        }
                        isValid = false;
                    } else {
                        if (errorEl) errorEl.style.display = 'none';
                    }
                } else {
                    if (errorEl) errorEl.style.display = 'none';
                }
            }
        });

        // Cek Input File (Foto)
        const photoInput = document.getElementById('photos');
        const photoError = document.getElementById('error_photos');
        if (photoInput.files.length === 0) {
            photoError.style.display = 'block';
            isValid = false;
        } else {
            photoError.style.display = 'none';
        }

        // Update status tombol submit
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

    // Cek validasi awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', validateForm);

</script>
@endsection