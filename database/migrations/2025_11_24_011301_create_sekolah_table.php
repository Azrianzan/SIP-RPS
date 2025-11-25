<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop jika sudah ada tabel sekolah yang eksis
        Schema::dropIfExists('sekolah');

        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->enum('jenjang', ['SD', 'SMP', 'SMA', 'SMK', 'SLB']);
            $table->text('alamat');
            $table->string('kabupaten_kota');
            $table->string('kecamatan')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('kepala_sekolah')->nullable();
            $table->integer('jumlah_siswa')->default(0);
            $table->enum('kondisi_sekolah', ['Baik', 'Cukup', 'Rusak Ringan', 'Rusak Berat'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
