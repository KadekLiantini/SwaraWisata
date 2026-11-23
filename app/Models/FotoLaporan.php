<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoLaporan extends Model
{
    public $timestamps = false;
    protected $table = 'foto_laporan';
    protected $primaryKey = 'id_foto_laporan';
    protected $fillable = ['id_laporan', 'path'];

    public function laporan() { return $this->belongsTo(Laporan::class, 'id_laporan', 'id_laporan'); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->path); }
}
// FotoRiwayat: table foto_riwayat, PK id_foto_riwayat, fillable id_riwayat & path, belongsTo RiwayatPenanganan
