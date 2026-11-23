<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Akun;
use App\Models\Destinasi;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Akun::create(['username' => 'admin', 'password' => 'admin12345', 'role' => Akun::ROLE_ADMIN]);
        Akun::create(['username' => 'wisatawan1', 'password' => 'password', 'role' => Akun::ROLE_WISATAWAN]);
        Akun::create(['username' => 'wisatawan2', 'password' => 'password', 'role' => Akun::ROLE_WISATAWAN]);

        Akun::create([
            'username'     => 'pengelola_kuta',
            'password'     => 'password',
            'role'         => Akun::ROLE_PENGELOLA,
            'id_destinasi' => Destinasi::where('nama', 'Pantai Kuta')->value('id_destinasi'),
        ]);
    }
}
