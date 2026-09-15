<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product; // Pastikan Model Product sudah ada

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar nama produk dari chat Anda
        $dataProduk = [
            "Botol HDPE MHS 100 mL", "Botol HDPE MHS 100 mL - Kuning", "Botol HDPE MHS 100 mL - Merah", "Botol HDPE MHS 100 mL - Hitam",
            "Tutup PP MHS 100 mL", "Tutup PP MHS 100 mL - Kuning", "Tutup PP MHS 100 mL - Merah", "Tutup PP MHS 100 mL - Hitam",
            "Plug LDPE MHS 100 mL", "Botol PET Sinai 60 mL 9.5", "Tutup PP Sinai 60 mL", "Botol PET Sari Kurma 350gr Clear P23",
            "Tutup PP Sari Kurma 350 Gram", "Botol PET Madu HNI 190 mL Clear P23", "Tutup PP Madu HNI 190 mL", "Botol PET Minyak Kayu Putih 100 mL",
            "Tutup PP Minyak Kayu Putih 100 mL", "Botol PET Minyak Telon 100 mL P12.5", "Tutup PP Minyak Telon 100 mL", "Botol PET Sano 73 mL",
            "Tutup HDPE Putih Sano 73 mL", "Botol PET Zidavit 200 mL Clear P30", "Tutup HDPE Sano Zidavit", "Botol PET Deep Olive 250 mL",
            "Tutup PP Deep Olive", "Botol PET Extrafood 200 mL Clear P30", "Tutup HDPE Extrafood", "Botol HDPE Deep Squa 60 mL White",
            "Tutup PP Deep Squa 60 mL White", "Botol HDPE Deep Squa 100 mL White", "Tutup PP Deep Squa 100 mL White", "Botol PET Sano 200 mL Amber P30 - Polos",
            "Tutup HDPE Sano - Namasindo", "Botol PET Sano 135 mL Clear P22", "Botol PET Waji Oil 100 mL Hitam Transparan P17", "Pump Spray Waji Oil",
            "Tutup Waji Oil", "Botol PET Waji Oil 65 mL Hitam Transparan P17", "Tutup Jamur D24 Hitam", "Tutup HDPE Sano Gold - Indotirta",
            "Botol PET Sano 200 mL Amber P30 - BW", "Tutup HDPE Sano - Indotirta", "Botol PET Gizidat 130 mL Clear P14", "Tutup HDPE Gizidat Putih",
            "Botol PET Zam-zam 100 mL Clear P16", "Tutup HDPE Zam-zam Lip Ring 100 mL", "Botol PET Zam Zam Fluba 100 mL Clear P16", "Botol PET Gizidat 60 mL Clear P10.7",
            "Botol PET Kale 150 mL Clear P16.5", "Tutup HDPE Gizidat Hitam", "Botol PET Kale 250 mL Clear P27", "Tutup HDPE Kale D38 Hitam",
            "Botol PET Sano 135 mL Amber P19", "Botol PET Zam-zam 60 mL Clear P10.8", "Tutup HDPE D30 Putih", "Botol PET Kapsul D38 100 mL Amber",
            "Tutup HDPE Kapsul Hitam", "Tutup HDPE Kapsul Gold", "Botol PET Kapsul D38 135 mL Putih", "Tutup HDPE Kapsul D38 Putih-Polos",
            "Tutup HDPE Kapsul D38 Double List Gold", "Botol PET Kapsul D38 135 mL Hitam", "Tutup HDPE Kapsul D38 Hitam-Polos", "Botol PET Sano 200 mL Amber P30 - TJI",
            "Botol PET Round 200 mL Clear P30", "Botol PET Vitabumin 130 mL Clear P22", "Tutup HDPE Vitabumin 130 mL", "Botol PET Zam-zam 100 mL Amber P19",
            "Botol PET Madu TJ 150 mL Clear", "Botol PET Madu TJ 250 mL Clear", "Tutup Fliptop Madu Kuning Membran", "Botol HDPE CMM 150 mL",
            "Botol PET SHL 350 mL Clear P42", "Tutup HDPE SHL 350 mL", "Botol HDPE ZJP 266 mL", "Botol HDPE ZJP 465 mL", "Botol PET ZJP 500 mL",
            "Jeriken HDPE ZJP 5 Liter", "Tutup PP Jeriken ZJP 5 Liter", "Botol PET PUM 600 mL", "Tutup HDPE PUM 600 mL", "Botol PET Kapsul PS 60 mL Hitam",
            "Tutup PP Kapsul List Gold PS 60 mL + Stamping Logo", "Plug PP Kapsul List Gold PS 60 mL", "Botol PET Vicofood 180 mL Clear P27 Bulat",
            "Tutup PP 180 Bulat", "Botol PET Vicofood 250 mL Clear P27 Kotak", "Tutup PP 250 Kotak", "Botol PET Sano 200 mL Clear P30 - Ardhi Jaya",
            "Botol PET Kapsul D38 100 mL Clear", "Tutup PP Kapsul D38 Natural", "Tutup PP Kapsul D38 Putih Polos", "Botol PET AMDK 330 mL Bluewis P9.1",
            "Botol PET AMDK 600 mL Bluewis P12", "Botol PET SterIlyn 500 mL (tanpa tutup)", "Botol HDPE Bodywash 250 mL Kuning (Brightening)",
            "Botol HDPE Bodywash 250 mL Coral (Protecting)", "Tutup PP Fliptop Jamur Body Wash 24 White", "Botol PET Kapsul 120 mL Hijau", "Tutup PP Kapsul List Gold 120 mL",
            "Botol HDPE Vermint 30 mL Orange", "Tutup PP Vermint Orange 30mL Stamping", "Botol HDPE Vermint 60 mL Orange", "Tutup PP Vermint Orange 60mL Stamping",
            "Botol PET Spiva 550 mL Clear P27 (tanpa tutup)", "Botol PET Spiva 380 mL Clear P27 (tanpa tutup)", "Botol HDPE Pupuk 1000ml Putih PK", "Tutup PP Pupuk Putih",
            "Plug LDPE Pupuk Natural", "Botol HDPE Pupuk 1000ml Kuning Povidione", "Tutup PP Pupuk Merah", "Tutup PP Kapsul D38 Putih List Silver",
            "Tutup PP Kapsul D38 Putih List Gold", "Botol PET Kapsul D38 135 mL Clear", "Botol PET Madu HNI 190 mL Clear P23 - Polos", "Pump Spray D24 Hitam",
            "Botol HDPE Vermint 60 mL Hijau", "Tutup PP Vermint Hijau 60mL Stamping", "Botol PET Susu UHT", "Tutup HDPE Susu UHT", "Botol PET Zam-zam 100 mL Clear JD",
            "Botol PET Susu D30 250 mL Clear JD", "Tutup PP D30 Zam-zam Putih JD", "Botol PET Zam-zam Susu 130 mL Clear IP", "Tutup PP Zam-zam Susu 130 mL IP",
            "Botol HDPE Kapsul 100 mL Putih", "Tutup PP Kapsul Naturonal + Foam Alu", "Botol PET Kale 500 mL Clear P42", "Botol PET Kale 250 mL Clear P27 (Free Item)",
            "Tutup HDPE Kale D38 Hitam (Free Item)", "Botol PET Kale 500 mL Clear P42 (Free Item)", "Tutup HDPE Kale D38 Putih (Free Item)", "Ember Plastik PP 4 Lt + Handle",
            "Ember Plastik PP 18 Lt + Tutup Putih + Handle", "Cup 120 mL HD", "Cup 200 mL KP", "Botol PET 330 mL SN30 Clear NO", "Botol PET 220 mL SN30 Clear KP",
            "Botol PET 600 mL SN30 Clear NO", "Botol PET 1500 mL SN30 Clear PB", "Tutup HDPE Orange SN30 OP", "Botol PET 330 mL SN30 Bluewis 9.1",
            "Botol PET 600 mL SN30 Bluewis 12", "Tutup HDPE Biru Muda SN30", "Botol PET 330 mL LN30 Clear NO", "Botol PET 600 mL LN30 Clear NO",
            "Botol PET 1500 mL LN30 Clear NO", "Tutup HDPE Putih LN30", "POT Cream 10gr Putih", "Plug POT Cream 10gr Clear", "Tutup POT Cream 10gr Putih",
            "Sendok Takar Susu", "Tutup HDPE Biru LN30", "Tutup PP Kapsul + Foam Alu", "Botol PET Cimory 250 mL Clear P23", "Tutup PP Kapsul 100 mL",
            "Botol PET Kapsul D38 100 mL Putih", "Tutup PP Kapsul D38 Hitam Double List Gold", "Botol PET Body Mist 30 mL Pink", "Botol PET Body Mist 60 mL Pink",
            "Botol PET Body Wash 250 mL Frosted", "Botol PET Madu 190 mL Clear P23", "Tutup PP Madu 190 mL Kuning", "Botol PET Sari Kurma 350gr Clear P23 - Polos",
            "Tutup PP Kapsul D38 Hitam List Gold"
        ];

        // Hapus duplikat nama agar database bersih
        $dataProdukUnik = array_unique($dataProduk);

        foreach ($dataProdukUnik as $index => $namaProduk) {
            // Generate QR Code otomatis: PRD-001, PRD-002, dst
            $qrCode = 'PRD-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Cek agar tidak duplikat saat seeding ulang
            Product::firstOrCreate(
                ['name' => $namaProduk], // Cek berdasarkan nama
                [
                    'qr_code' => $qrCode,
                    'status' => 'aktif'
                ]
            );
        }
    }
}