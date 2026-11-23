<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKerusakan extends Model
{
    public $timestamps = false;
    protected $table = 'kategori_kerusakan';
    protected $primaryKey = 'id_kategori_kerusakan';
    protected $fillable = ['nama', 'deskripsi'];

    public function laporan() { return $this->hasMany(Laporan::class, 'id_kategori_kerusakan', 'id_kategori_kerusakan'); }
}