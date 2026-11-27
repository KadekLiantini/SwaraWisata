<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destinasi extends Model
{
    const UPDATED_AT = null;
    protected $table = 'destinasi';
    protected $primaryKey = 'id_destinasi';
    protected $fillable = ['id_kecamatan', 'id_kategori_wisata', 'nama', 'deskripsi', 'foto', 'link_maps'];
    protected $casts = ['is_active' => 'boolean'];

    public function kecamatan()      { return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan'); }
    public function kategoriWisata() { return $this->belongsTo(KategoriWisata::class, 'id_kategori_wisata', 'id_kategori_wisata'); }
    public function laporan()        { return $this->hasMany(Laporan::class, 'id_destinasi', 'id_destinasi'); }
    public function pengelola()      { return $this->hasOne(Akun::class, 'id_destinasi', 'id_destinasi'); }
    public function pengajuanAsal()  { return $this->hasOne(PengajuanDestinasi::class, 'id_destinasi', 'id_destinasi'); }

    /** Dipakai landing page: Destinasi::terbanyakDilaporkan()->take(3)->get() */
    public function scopeTerbanyakDilaporkan($query)
    {
        return $query->withCount('laporan')->orderByDesc('laporan_count');
    }

    /** Destinasi yang masih aktif ditampilkan. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Destinasi yang dinonaktifkan oleh admin. */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
