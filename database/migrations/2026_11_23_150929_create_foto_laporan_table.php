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
        // foto_laporan
        Schema::create('foto_laporan', function (Blueprint $table) {
            $table->id('id_foto_laporan');
            $table->foreignId('id_laporan')->constrained('laporan', 'id_laporan')->cascadeOnDelete();
            $table->string('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_laporan');
    }
};
