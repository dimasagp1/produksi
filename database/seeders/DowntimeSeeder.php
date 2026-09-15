<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Downtime;

class DowntimeSeeder extends Seeder
{
    public function run(): void
    {
        $downtimeReasons = [
            'Breakdown / Failure (Kerusakan Mesin)',
            'Tooling Failure (Kerusakan Tool/Mold)',
            'Setup & Changeover (Persiapan/Ganti Setup)',
            'Minor Stoppages / Jams (Stop Singkat/Macam)',
            'Lack of Material (Menunggu Bahan Baku)',
            'Lack of Operator (Kurang Operator)',
            'Scheduled Maintenance (Perawatan Terjadwal)',
            'QC Hold / Inspection (Menunggu Cek Kualitas)',
            'Cleaning (Pembersihan)',
        ];

        foreach ($downtimeReasons as $reason) {
            Downtime::firstOrCreate(
                ['name' => $reason], 
                ['status' => 'active']
            );
        }
    }
}