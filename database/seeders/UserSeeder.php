<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin Herbatech',
                'email' => 'superadmin@herbatech.com',
                'role' => 'super_admin',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Administrator Herbatech',
                'email' => 'admin@herbatech.com',
                'role' => 'super_admin',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'General Manager Herbatech',
                'email' => 'gm@herbatech.com',
                'role' => 'manager',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'SPV Produksi Herbatech',
                'email' => 'spv@herbatech.com',
                'role' => 'spv',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Admin Produksi Herbatech',
                'email' => 'produksi@herbatech.com',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Admin PPIC Herbatech',
                'email' => 'ppic@herbatech.com',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Koordinator Produksi Herbatech',
                'email' => 'leader@herbatech.com',
                'role' => 'leader',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Operator Herbatech',
                'email' => 'operator@herbatech.com',
                'role' => 'operator',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Hapus dummy user default jika ada
        User::where('email', 'test@example.com')->delete();
    }
}
