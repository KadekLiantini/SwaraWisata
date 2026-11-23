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
        // laporan
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('id_akun')->constrained('akun', 'id_akun');
            $table->foreignId('id_destinasi')->constrained('destinasi', 'id_destinasi');
            $table->foreignId('id_kategori_kerusakan')->constrained('kategori_kerusakan', 'id_kategori_kerusakan');
            $table->string('judul', 150);
            $table->text('deskripsi');
            $table->string('lokasi_spesifik');
            $table->enum('status', ['belum_dilihat', 'dilihat', 'ditindaklanjuti', 'selesai'])->default('belum_dilihat');
            $table->timestamp('dilihat_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['id_destinasi', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
