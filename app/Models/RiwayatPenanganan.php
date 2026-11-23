<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPenanganan extends Model
{
    public const TINDAK_LANJUT = 'tindak_lanjut';
    public const PENYELESAIAN  = 'penyelesaian';

    protected $table = 'riwayat_penanganan';
    protected $primaryKey = 'id_riwayat';
    protected $fillable = ['id_laporan', 'id_akun', 'jenis_tindakan', 'deskripsi'];

    public function laporan()   { return $this->belongsTo(Laporan::class, 'id_laporan', 'id_laporan'); }
    public function pengelola() { return $this->belongsTo(Akun::class, 'id_akun', 'id_akun'); }
    public function foto()      { return $this->hasMany(FotoRiwayat::class, 'id_riwayat', 'id_riwayat'); }
    /** Hanya komentar utama; balasan diambil lewat Komentar::balasan */
    public function komentar()  { return $this->hasMany(Komentar::class, 'id_riwayat', 'id_riwayat')->whereNull('id_parent')->oldest(); }

    public function milik(Akun $akun): bool { return $this->id_akun === $akun->id_akun; }
}