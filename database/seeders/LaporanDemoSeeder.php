<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Laporan;
use App\Models\Destinasi;
use App\Models\Akun;
use App\Models\KategoriKerusakan;

class LaporanDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelapor = Akun::where('role', Akun::ROLE_WISATAWAN)->get();
        $kategori = KategoriKerusakan::pluck('id_kategori_kerusakan');
        $bobot = ['Pantai Kuta' => 6, 'Pura Tanah Lot' => 4, 'Pantai Sanur' => 3, 'Danau Batur' => 1];

        foreach ($bobot as $namaDestinasi => $jumlah) {
            $destinasi = Destinasi::where('nama', $namaDestinasi)->first();
            for ($i = 1; $i <= $jumlah; $i++) {
                $laporan = Laporan::create([
                    'id_akun'               => $pelapor->random()->id_akun,
                    'id_destinasi'          => $destinasi->id_destinasi,
                    'id_kategori_kerusakan' => $kategori->random(),
                    'judul'                 => "Contoh laporan {$i} di {$namaDestinasi}",
                    'deskripsi'             => 'Data demo untuk pengujian tampilan.',
                    'lokasi_spesifik'       => 'Dekat pintu masuk',
                ]);
                $laporan->foto()->create(['path' => 'laporan/contoh.jpg']);
            }
        }
    }
}
