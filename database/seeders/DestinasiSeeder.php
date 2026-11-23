<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Destinasi;
use App\Models\Kecamatan;
use App\Models\KategoriWisata;

class DestinasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $data = [
            // [nama, kecamatan, kategori wisata]
            ['Pantai Kuta',            'Kuta',             'Pantai'],
            ['Pura Luhur Uluwatu',     'Kuta Selatan',     'Pura'],
            ['Pantai Sanur',           'Denpasar Selatan', 'Pantai'],
            ['Pura Tirta Empul',       'Tampaksiring',     'Pura'],
            ['Pura Tanah Lot',         'Kediri',           'Pura'],
            ['Danau Batur',            'Kintamani',        'Danau'],
            ['Air Terjun Gitgit',      'Sukasada',         'Air Terjun'],
            ['Taman Soekasada Ujung',  'Karangasem',       'Taman'],
            ['Pantai Kelingking',      'Nusa Penida',      'Pantai'],
            ['Pura Rambut Siwi',       'Mendoyo',          'Pura'],
        ];

        foreach ($data as [$nama, $kecamatan, $kategori]) {
            Destinasi::create([
                'nama'               => $nama,
                'id_kecamatan'       => Kecamatan::where('nama', $kecamatan)->value('id_kecamatan'),
                'id_kategori_wisata' => KategoriWisata::where('nama', $kategori)->value('id_kategori_wisata'),
                'deskripsi'          => "Destinasi wisata {$nama}.",
                'link_maps'          => 'https://maps.google.com/?q=' . urlencode($nama . ' Bali'),
            ]);
        }
    }
}
