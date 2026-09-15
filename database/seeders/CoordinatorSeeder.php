<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coordinator;
class CoordinatorSeeder extends Seeder {
    public function run() {
        Coordinator::firstOrCreate(['name' => 'Alip'], ['status' => 'active']);
        Coordinator::firstOrCreate(['name' => 'Hanifan'], ['status' => 'active']);
        Coordinator::firstOrCreate(['name' => 'Alvin'], ['status' => 'active']);
    }
}