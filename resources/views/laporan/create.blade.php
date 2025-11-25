@extends('layouts.app')

@section('title', 'Form Laporan Progres')

@section('content')
<div class="page-header">
    <h2>Form Laporan Progres</h2>
    <ul class="breadcrumb">
        <li>Home</li>
        <li><a href="{{ route('proyek.show', $proyek->id) }}">Detail Proyek</a></li>
        <li>Form Laporan</li>
    </ul>
</div>

<div class="form-container" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <form action="{{ route('laporan.store', $proyek->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="project">Proyek</label>
            <input type="text" class="form-control" value="{{ $proyek->nama_proyek }}" readonly style="background-color: #e9ecef;">
            <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
        </div>
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="report-date">Tanggal Laporan</label>
            <input type="date" name="tanggal_laporan" id="report-date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group">
                <label for="physical-progress">Progres Fisik (%)</label>
                <input type="number" name="progres_fisik" id="physical-progress" class="form-control" min="0" max="100" placeholder="0-100" required>
            </div>
            
            <div class="form-group">
                <label for="financial-progress">Progres Keuangan (%)</label>
                <input type="number" name="progres_keuangan" id="financial-progress" class="form-control" min="0" max="100" placeholder="0-100" required>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="reporter">Nama Penanggung Jawab Lapangan</label>
            <input type="text" class="form-control" value="{{ Auth::user()->nama }}" readonly style="background-color: #e9ecef;">
        </div>
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="report-title">Judul Laporan</label>
            <input type="text" name="judul_laporan" id="report-title" class="form-control" placeholder="Contoh: Laporan Minggu ke-3 Pemasangan Atap" required>
        </div>
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="description">Keterangan / Catatan</label>
            <textarea name="keterangan" id="description" class="form-control" rows="4" placeholder="Deskripsi detail kemajuan pekerjaan, kendala yang dihadapi, dan rencana selanjutnya..." required></textarea>
        </div>
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="photos">Unggah Foto Dokumentasi</label>
            <input type="file" name="photos[]" id="photos" class="form-control" multiple accept="image/*" onchange="previewImages()">
            <small style="color: #666;">Unggah foto progres pekerjaan (Bisa pilih banyak sekaligus)</small>
        </div>
        
        <div id="photo-gallery" class="photo-gallery" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 2rem;"></div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">Kirim Laporan</button>
            <a href="{{ route('proyek.show', $proyek->id) }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<script>
    // Script sederhana untuk preview gambar sebelum upload
    function previewImages() {
        var preview = document.querySelector('#photo-gallery');
        preview.innerHTML = '';
        var files   = document.querySelector('input[type=file]').files;

        function readAndPreview(file) {
            // Validasi tipe gambar
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
    }
</script>
@endsection