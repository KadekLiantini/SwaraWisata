<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $table = 'komentar';
    protected $primaryKey = 'id_komentar';
    protected $fillable = ['id_riwayat', 'id_akun', 'id_parent', 'isi'];

    public function riwayat() { return $this->belongsTo(RiwayatPenanganan::class, 'id_riwayat', 'id_riwayat'); }
    public function penulis() { return $this->belongsTo(Akun::class, 'id_akun', 'id_akun'); }
    public function induk()   { return $this->belongsTo(Komentar::class, 'id_parent', 'id_komentar'); }
    public function balasan() { return $this->hasMany(Komentar::class, 'id_parent', 'id_komentar')->oldest(); }

    public function isBalasan(): bool       { return $this->id_parent !== null; }
    public function milik(Akun $akun): bool { return $this->id_akun === $akun->id_akun; }
}