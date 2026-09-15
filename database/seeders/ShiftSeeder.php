<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            'Shift 1',
            'Shift 2',
            'Shift 3'
        ];

        foreach ($shifts as $shiftName) {
            Shift::firstOrCreate(['name' => $shiftName], ['status' => 'active']);
        }
    }
}