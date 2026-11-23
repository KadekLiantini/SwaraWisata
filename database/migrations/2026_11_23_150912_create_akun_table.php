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
        // akun
        Schema::create('akun', function (Blueprint $table) {
            $table->id('id_akun');
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->enum('role', ['wisatawan', 'pengelola', 'admin'])->default('wisatawan');
            $table->foreignId('id_destinasi')->nullable()->unique()
                ->constrained('destinasi', 'id_destinasi')->nullOnDelete();
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};
