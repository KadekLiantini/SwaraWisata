<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Akun extends Authenticatable
{
    public const ROLE_WISATAWAN = 'wisatawan';
    public const ROLE_PENGELOLA = 'pengelola';
    public const ROLE_ADMIN = 'admin';

    const UPDATED_AT = null;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    protected $fillable = ['username', 'password', 'role', 'id_destinasi'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function isAdmin(): bool     { return $this->role === self::ROLE_ADMIN; }
    public function isPengelola(): bool { return $this->role === self::ROLE_PENGELOLA; }
    public function isWisatawan(): bool { return $this->role === self::ROLE_WISATAWAN; }

    /** Tujuan setelah login (aturan bisnis no. 3) */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN     => route('admin.dashboard'),
            self::ROLE_PENGELOLA => route('pengelola.dashboard'),
            default              => route('wisatawan.beranda'),
        };
    }

    public function destinasi()         { return $this->belongsTo(Destinasi::class, 'id_destinasi', 'id_destinasi'); }
    public function laporan()           { return $this->hasMany(Laporan::class, 'id_akun', 'id_akun'); }
    public function riwayatPenanganan() { return $this->hasMany(RiwayatPenanganan::class, 'id_akun', 'id_akun'); }
    public function komentar()          { return $this->hasMany(Komentar::class, 'id_akun', 'id_akun'); }
    public function pengajuan()         { return $this->hasMany(PengajuanDestinasi::class, 'id_akun', 'id_akun'); }
}