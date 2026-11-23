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
        // kategori_kerusakan
        Schema::create('kategori_kerusakan', function (Blueprint $table) {
            $table->id('id_kategori_kerusakan');
            $table->string('nama', 100)->unique();
            $table->text('deskripsi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_kerusakan');
    }
};
