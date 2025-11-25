<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoLaporan extends Model
{
    protected $table = 'foto_laporan';
    
    protected $guarded = ['id'];

    // Relasi balik ke Laporan
    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }
}
