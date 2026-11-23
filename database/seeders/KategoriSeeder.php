<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriKerusakan;
use App\Models\KategoriWisata;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Pantai', 'Pura', 'Air Terjun', 'Danau', 'Gunung', 'Taman', 'Sawah/Terasering', 'Desa Wisata', 'Museum', 'Hutan'] as $nama) {
            KategoriWisata::create(['nama' => $nama]);
        }

        $kerusakan = [
            'Toilet/Kamar Mandi'      => 'Toilet rusak, kotor, air tidak mengalir, atau tidak tersedia.',
            'Jalan dan Jalur Pejalan Kaki' => 'Jalan berlubang, jalur licin, tangga rusak.',
            'Kebersihan Lingkungan'   => 'Sampah menumpuk atau area tidak terawat.',
            'Penerangan'              => 'Lampu mati atau area gelap.',
            'Area Parkir'             => 'Parkir rusak, sempit, atau tidak tertata.',
            'Pagar Pengaman'          => 'Pagar atau pembatas rusak sehingga membahayakan.',
            'Tempat Sampah'           => 'Tempat sampah rusak atau tidak tersedia.',
            'Papan Informasi'         => 'Papan petunjuk rusak, hilang, atau tidak terbaca.',
            'Fasilitas Umum Lainnya'  => 'Tempat ibadah, tempat istirahat, dan fasilitas lain.',
        ];
        foreach ($kerusakan as $nama => $deskripsi) {
            KategoriKerusakan::create(compact('nama', 'deskripsi'));
        }
    }
}
