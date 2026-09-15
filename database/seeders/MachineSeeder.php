<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar mesin dari Anda
        $machines = [
            'Stretch Blow Manual',
            'Hot Stamping Roll',
            'VICTOR MSZ30.1',
            'VICTOR MSZ30.2',
            'ASB-12M',
            'CHUMPOWER CPSB TSS 3000',
            'LANCING AT-150T.1',
            'LANCING AT-150T.2',
            'NIGATA CN75E',
            'ARBURG 420M',
            'LANCING AT-300T',
            'Vertical Mixing 100 kg',
        ];

        foreach ($machines as $index => $machineName) {
            // Kita buat kode mesin otomatis (MC-001, MC-002, dst)
            // str_pad digunakan agar angka 1 menjadi 001
            $code = 'MC-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Machine::create([
                'name' => $machineName,
                'code' => $code,
            ]);
        }
    }
}