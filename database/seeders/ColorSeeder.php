<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            'Putih', 'Hitam', 'Merah', 'Kuning', 'Hijau', 'Biru', 'Biru Muda', 'Pink', 'Orange', 
            'Coral', 'Clear', 'Frosted', 'Hitam Transparan', 'Gold', 'Silver', 'List Gold', 
            'List Silver', 'Double List Gold', 'Natural', 'Amber', 'Deep Olive', 'Bluewis', 
            'Kuning Povidione', 'Kuning Brightening', 'Coral Protecting', 'Orange Vermint', 
            'Hijau Vermint'
        ];

        foreach ($colors as $colorName) {
            Color::firstOrCreate(['name' => $colorName], ['status' => 'active']);
        }
    }
}