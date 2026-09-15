<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RejectCategory;
use App\Models\RejectItem;

class RejectSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'GA' => ['Black Spot', 'Contamination'],
            'RECYCLE' => ['Bubble', 'Flash', 'Short Mould', 'Warna TMS', 'Weld Line']
        ];

        foreach ($data as $code => $items) {
            $category = RejectCategory::firstOrCreate(
                ['code' => $code],
                ['name' => 'REJECT ' . $code, 'status' => 'active']
            );

            foreach ($items as $itemName) {
                RejectItem::firstOrCreate([
                    'name' => $itemName,
                    'category_id' => $category->id
                ], ['status' => 'active']);
            }
        }
    }
}