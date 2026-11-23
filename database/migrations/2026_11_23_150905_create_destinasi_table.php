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
        // destinasi
        Schema::create('destinasi', function (Blueprint $table) {
            $table->id('id_destinasi');
            $table->foreignId('id_kecamatan')->constrained('kecamatan', 'id_kecamatan');
            $table->foreignId('id_kategori_wisata')->constrained('kategori_wisata', 'id_kategori_wisata');
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->string('link_maps', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinasi');
    }
};
