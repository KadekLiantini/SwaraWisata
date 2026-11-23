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
        // riwayat_penanganan
        Schema::create('riwayat_penanganan', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_laporan')->constrained('laporan', 'id_laporan')->cascadeOnDelete();
            $table->foreignId('id_akun')->constrained('akun', 'id_akun');
            $table->enum('jenis_tindakan', ['tindak_lanjut', 'penyelesaian']);
            $table->text('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_penanganan');
    }
};
