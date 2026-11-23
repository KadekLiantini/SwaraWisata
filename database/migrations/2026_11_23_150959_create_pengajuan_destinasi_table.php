<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // pengajuan_destinasi
        Schema::create('pengajuan_destinasi', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun');
            $table->foreignId('id_verifikator')->nullable()->constrained('akun', 'id_akun')->nullOnDelete();
            $table->foreignId('id_kecamatan')->constrained('kecamatan', 'id_kecamatan');
            $table->foreignId('id_kategori_wisata')->constrained('kategori_wisata', 'id_kategori_wisata');
            $table->foreignId('id_destinasi')->nullable()->constrained('destinasi', 'id_destinasi')->nullOnDelete();
            $table->string('nama_lokasi', 150);
            $table->string('link_maps', 500);
            $table->enum('status', ['menunggu_verifikasi', 'disetujui', 'ditolak'])->default('menunggu_verifikasi');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('diverifikasi_pada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_destinasi');
    }
};
