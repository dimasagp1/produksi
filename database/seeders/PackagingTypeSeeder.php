<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PackagingType;

class PackagingTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Data Kemasan dengan detail konversi: [Nama Kemasan, Kuantitas Isi (PCS), Unit Isi]
        $packagingData = [
            // 1 Dus Karton A1 berisi 200 Botol
            ['Dus Karton A1', 200, 'Botol', 'active'], 
            
            // 1 Dus Karton B2 berisi 500 Tutup
            ['Dus Karton B2', 500, 'Tutup', 'active'],
            
            // 1 Bag Plastik berisi 1000 Preform
            ['Bag Plastik Besar', 1000, 'Preform', 'active'],
            
            // 1 Pallet berisi 20 Dus (contoh untuk unit yang lebih besar)
            ['Pallet Kayu', 20, 'Dus', 'active'],
            
            // 1 Karton C5 berisi 400 Unit Mix
            ['Karton C5', 400, 'Unit', 'inactive'],
        ];

        foreach ($packagingData as $data) {
            PackagingType::firstOrCreate(
                // Kriteria pencarian: hanya nama kemasan
                ['name' => $data[0]], 
                // Data yang akan disimpan
                [
                    'conversion_quantity' => $data[1],
                    'content_unit' => $data[2],
                    'status' => $data[3],
                ]
            );
        }
    }
}