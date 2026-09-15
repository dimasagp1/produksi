<?php
namespace App\Imports;

use App\Models\Coordinator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CoordinatorsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Mencegah duplikasi nama saat import
        if (isset($row['name']) && Coordinator::where('name', $row['name'])->exists()) {
            return null; 
        }

        return new Coordinator([
            'name'   => $row['name'],
            'status' => strtolower($row['status'] ?? 'active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:coordinators,name',
            'status' => 'required|in:active,inactive',
        ];
    }
}