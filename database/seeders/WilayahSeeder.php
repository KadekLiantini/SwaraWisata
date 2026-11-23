<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kabupaten;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Badung'        => ['Kuta Selatan', 'Kuta', 'Kuta Utara', 'Mengwi', 'Abiansemal', 'Petang'],
            'Bangli'        => ['Bangli', 'Susut', 'Tembuku', 'Kintamani'],
            'Buleleng'      => ['Gerokgak', 'Seririt', 'Busungbiu', 'Banjar', 'Sukasada', 'Buleleng', 'Sawan', 'Kubutambahan', 'Tejakula'],
            'Gianyar'       => ['Sukawati', 'Blahbatuh', 'Gianyar', 'Tampaksiring', 'Ubud', 'Tegallalang', 'Payangan'],
            'Jembrana'      => ['Melaya', 'Negara', 'Jembrana', 'Mendoyo', 'Pekutatan'],
            'Karangasem'    => ['Rendang', 'Sidemen', 'Manggis', 'Karangasem', 'Abang', 'Bebandem', 'Selat', 'Kubu'],
            'Klungkung'     => ['Nusa Penida', 'Banjarangkan', 'Klungkung', 'Dawan'],
            'Tabanan'       => ['Selemadeg', 'Selemadeg Timur', 'Selemadeg Barat', 'Kerambitan', 'Tabanan', 'Kediri', 'Marga', 'Baturiti', 'Penebel', 'Pupuan'],
            'Kota Denpasar' => ['Denpasar Selatan', 'Denpasar Timur', 'Denpasar Barat', 'Denpasar Utara'],
        ];

        foreach ($data as $kabupaten => $kecamatan) {
            Kabupaten::create(['nama' => $kabupaten])
                ->kecamatan()
                ->createMany(array_map(fn ($nama) => ['nama' => $nama], $kecamatan));
        }
    }
}
