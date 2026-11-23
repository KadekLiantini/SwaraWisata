<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    public $timestamps = false;
    protected $table = 'kecamatan';
    protected $primaryKey = 'id_kecamatan';
    protected $fillable = ['id_kabupaten', 'nama'];

    public function kabupaten() { return $this->belongsTo(Kabupaten::class, 'id_kabupaten', 'id_kabupaten'); }
    public function destinasi() { return $this->hasMany(Destinasi::class, 'id_kecamatan', 'id_kecamatan'); }
}