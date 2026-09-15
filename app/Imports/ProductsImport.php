<?php
namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['name'])) return null;

        return new Product([
            'name'    => $row['name'],
            'status'  => strtolower($row['status'] ?? 'aktif'),
        ]);
    }
}