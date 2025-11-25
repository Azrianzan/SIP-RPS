<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';
    
    // Kita gunakan guarded id agar semua kolom lain bisa diisi (mass assignment)
    protected $guarded = ['id'];

    // Relasi ke Proyek
    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    // Relasi ke User (Pelapor)
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // Relasi ke User (Validator) - Optional nanti
    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    // Relasi ke Foto Laporan
    public function foto()
    {
        return $this->hasMany(FotoLaporan::class);
    }
}