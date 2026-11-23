<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    public $timestamps = false;
    protected $table = 'kabupaten';
    protected $primaryKey = 'id_kabupaten';
    protected $fillable = ['nama'];

    public function kecamatan() { return $this->hasMany(Kecamatan::class, 'id_kabupaten', 'id_kabupaten'); }
}
