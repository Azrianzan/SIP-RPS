<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $table = 'proyek'; // Sesuai migration
    protected $guarded = ['id'];

    public function sekolah() {
        return $this->belongsTo(Sekolah::class);
    }
    
    // Jika ingin menampilkan nama PJL/Admin
    public function pjl() {
        return $this->belongsTo(User::class, 'pjl_id');
    }

    public function laporan() {
        return $this->hasMany(Laporan::class);
    }

    // Relasi ke User (Admin yang input)
    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}