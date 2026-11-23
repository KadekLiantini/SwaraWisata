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
        // kecamatan
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id('id_kecamatan');
            $table->foreignId('id_kabupaten')->constrained('kabupaten', 'id_kabupaten')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->unique(['id_kabupaten', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kecamatan');
    }
};
