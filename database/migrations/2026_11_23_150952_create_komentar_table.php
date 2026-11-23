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
        // komentar
        Schema::create('komentar', function (Blueprint $table) {
            $table->id('id_komentar');
            $table->foreignId('id_riwayat')->constrained('riwayat_penanganan', 'id_riwayat')->cascadeOnDelete();
            $table->foreignId('id_akun')->constrained('akun', 'id_akun');
            $table->foreignId('id_parent')->nullable()->constrained('komentar', 'id_komentar')->cascadeOnDelete();
            $table->text('isi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentar');
    }
};
