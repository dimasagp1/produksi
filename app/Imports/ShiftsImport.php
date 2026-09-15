<?php
namespace App\Imports;

use App\Models\Shift;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ShiftsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Mencegah duplikasi nama saat import
        if (isset($row['name']) && Shift::where('name', $row['name'])->exists()) {
            return null; 
        }

        return new Shift([
            'name'   => $row['name'],
            'status' => strtolower($row['status'] ?? 'active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:shifts,name',
            'status' => 'required|in:active,inactive',
        ];
    }
}