<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoRiwayat extends Model
{
    public $timestamps = false;
    protected $table = 'foto_riwayat';
    protected $primaryKey = 'id_foto_riwayat';
    protected $fillable = ['id_riwayat', 'path'];

    public function laporan() { return $this->belongsTo(RiwayatPenanganan::class, 'id_riwayat', 'id_riwayat'); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->path); }
}
// FotoRiwayat: table foto_riwayat, PK id_foto_riwayat, fillable id_riwayat & path, belongsTo RiwayatPenanganan
