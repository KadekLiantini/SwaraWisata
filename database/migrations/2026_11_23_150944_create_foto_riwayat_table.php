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
        // foto_riwayat
        Schema::create('foto_riwayat', function (Blueprint $table) {
            $table->id('id_foto_riwayat');
            $table->foreignId('id_riwayat')->constrained('riwayat_penanganan', 'id_riwayat')->cascadeOnDelete();
            $table->string('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_riwayat');
    }
};
