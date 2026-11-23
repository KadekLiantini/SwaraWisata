<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanDestinasi extends Model
{
    public const MENUNGGU  = 'menunggu_verifikasi';
    public const DISETUJUI = 'disetujui';
    public const DITOLAK   = 'ditolak';

    const UPDATED_AT = null;
    protected $table = 'pengajuan_destinasi';
    protected $primaryKey = 'id_pengajuan';
    protected $fillable = ['id_akun', 'id_verifikator', 'id_kecamatan', 'id_kategori_wisata', 'id_destinasi', 'nama_lokasi', 'link_maps', 'status', 'diverifikasi_pada'];
    protected $casts = ['diverifikasi_pada' => 'datetime'];

    public function pengaju()        { return $this->belongsTo(Akun::class, 'id_akun', 'id_akun'); }
    public function verifikator()    { return $this->belongsTo(Akun::class, 'id_verifikator', 'id_akun'); }
    public function kecamatan()      { return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan'); }
    public function kategoriWisata() { return $this->belongsTo(KategoriWisata::class, 'id_kategori_wisata', 'id_kategori_wisata'); }
    public function destinasi()      { return $this->belongsTo(Destinasi::class, 'id_destinasi', 'id_destinasi'); }
}