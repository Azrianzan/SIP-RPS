<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';
    
    protected $fillable = [
        'nama_sekolah', 'jenjang', 'alamat', 'kabupaten_kota', 
        'kecamatan', 'telepon', 'email', 'kepala_sekolah', 
        'jumlah_siswa', 'kondisi_sekolah'
    ];

    // Relasi: Satu Sekolah punya banyak Proyek
    public function proyek()
    {
        return $this->hasMany(Proyek::class);
    }
}