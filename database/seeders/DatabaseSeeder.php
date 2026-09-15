<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MenuSeeder::class,
            ColorSeeder::class,
            ShiftSeeder::class,
            CoordinatorSeeder::class,
            OperatorSeeder::class,
            PackagingTypeSeeder::class,
            MachineSeeder::class,
            RejectSeeder::class,
            DowntimeSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
