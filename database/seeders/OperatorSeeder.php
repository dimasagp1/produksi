<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Operator;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            'Budi Santoso', 'Eko Prasetyo', 'Rizky Kurniawan', 
            'Agus Setiawan', 'Doni Haryanto', 'Fajar Nugroho', 
            'Gilang Ramadhan', 'Hendra Wijaya', 'Indra Gunawan', 
            'Joko Susilo'
        ];

        foreach ($operators as $name) {
            Operator::firstOrCreate(['name' => $name], ['status' => 'active']);
        }
    }
}