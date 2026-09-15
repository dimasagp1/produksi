<?php
namespace App\Imports;

use App\Models\Color;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ColorsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Mencegah duplikasi nama saat import
        if (isset($row['name']) && Color::where('name', $row['name'])->exists()) {
            return null; 
        }

        return new Color([
            'name'   => $row['name'],
            'status' => strtolower($row['status'] ?? 'active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:colors,name',
            'status' => 'required|in:active,inactive',
        ];
    }
}