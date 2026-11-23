<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriWisata extends Model
{
    public $timestamps = false;
    protected $table = 'kategori_wisata';
    protected $primaryKey = 'id_kategori_wisata';
    protected $fillable = ['nama'];

    public function destinasi() { return $this->hasMany(Destinasi::class, 'id_kategori_wisata', 'id_kategori_wisata'); }
}
