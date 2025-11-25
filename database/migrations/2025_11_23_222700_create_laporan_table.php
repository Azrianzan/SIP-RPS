<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->onDelete('cascade');
            $table->foreignId('pelapor_id')->constrained('users');
            $table->foreignId('validator_id')->nullable()->constrained('users');
            $table->date('tanggal_laporan');
            $table->decimal('progres_fisik', 5, 2);
            $table->decimal('progres_keuangan', 5, 2);
            $table->text('keterangan')->nullable();
            $table->string('status_validasi', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
