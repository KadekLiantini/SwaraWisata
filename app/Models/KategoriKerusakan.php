<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KategoriKerusakan extends Model
{
    public $timestamps = false;
    protected $table = 'kategori_kerusakan';
    protected $primaryKey = 'id_kategori_kerusakan';
    protected $fillable = ['nama', 'deskripsi'];
    protected $casts = ['is_active' => 'boolean'];

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_kategori_kerusakan', 'id_kategori_kerusakan');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

        /** Nama ikon Material Symbols sesuai jenis kerusakan (dipakai kartu laporan) */
    public function icon(): string
    {
        $nama = Str::lower($this->nama);

        return match (true) {
            str_contains($nama, 'toilet'), str_contains($nama, 'sanitasi')    => 'water_drop',
            str_contains($nama, 'jalan'), str_contains($nama, 'jalur')        => 'directions_walk',
            str_contains($nama, 'kebersihan'), str_contains($nama, 'sampah')  => 'delete',
            str_contains($nama, 'penerangan'), str_contains($nama, 'lampu')   => 'lightbulb',
            str_contains($nama, 'parkir')                                     => 'local_parking',
            str_contains($nama, 'pagar'), str_contains($nama, 'keamanan')     => 'shield',
            str_contains($nama, 'papan'), str_contains($nama, 'rambu')        => 'signpost',
            default                                                           => 'construction',
        };
    }
}