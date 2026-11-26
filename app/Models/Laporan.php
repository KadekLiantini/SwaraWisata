<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use SoftDeletes;

    public const STATUS_BELUM_DILIHAT   = 'belum_dilihat';
    public const STATUS_DILIHAT         = 'dilihat';
    public const STATUS_DITINDAKLANJUTI = 'ditindaklanjuti';
    public const STATUS_SELESAI         = 'selesai';

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';
    protected $fillable = ['id_akun', 'id_destinasi', 'id_kategori_kerusakan', 'judul', 'deskripsi', 'lokasi_spesifik', 'status', 'dilihat_pada'];
    protected $casts = ['dilihat_pada' => 'datetime'];

    public function pelapor()           { return $this->belongsTo(Akun::class, 'id_akun', 'id_akun'); }
    public function destinasi()         { return $this->belongsTo(Destinasi::class, 'id_destinasi', 'id_destinasi'); }
    public function kategoriKerusakan() { return $this->belongsTo(KategoriKerusakan::class, 'id_kategori_kerusakan', 'id_kategori_kerusakan'); }
    public function foto()              { return $this->hasMany(FotoLaporan::class, 'id_laporan', 'id_laporan'); }
    public function riwayat()           { return $this->hasMany(RiwayatPenanganan::class, 'id_laporan', 'id_laporan')->oldest(); }

    /** Aturan no. 7: dipanggil saat pengelola membuka detail */
    public function tandaiDilihat(): void
    {
        if ($this->status === self::STATUS_BELUM_DILIHAT) {
            $this->update(['status' => self::STATUS_DILIHAT, 'dilihat_pada' => now()]);
        }
    }

    /** Aturan no. 18: dipanggil setelah riwayat dibuat, diedit, atau dihapus */
    public function hitungUlangStatus(): void
    {
        $jenis = $this->riwayat()->pluck('jenis_tindakan');

        $this->status = match (true) {
            $jenis->contains('penyelesaian')  => self::STATUS_SELESAI,
            $jenis->contains('tindak_lanjut') => self::STATUS_DITINDAKLANJUTI,
            $this->dilihat_pada !== null      => self::STATUS_DILIHAT,
            default                           => self::STATUS_BELUM_DILIHAT,
        };
        $this->save();
    }

    /** Aturan no. 17 */
    public function milik(Akun $akun): bool
    {
        return $this->id_akun === $akun->id_akun;
    }

    /** ID laporan untuk ditampilkan, contoh: #16 */
    public function getKodeAttribute(): string
    {
        return '#'.$this->id_laporan;
    }

    /** Laporan hanya boleh diedit/dihapus sebelum pengelola menindaklanjuti (aturan no. 15 & 17) */
    public function bisaDiubah(): bool
    {
        return in_array($this->status, [self::STATUS_BELUM_DILIHAT, self::STATUS_DILIHAT], true);
    }
}