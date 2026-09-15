<?php
namespace App\Imports;

use App\Models\PackagingType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PackagingTypesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        if (isset($row['name']) && PackagingType::where('name', $row['name'])->exists()) {
            return null; 
        }

        return new PackagingType([
            'name'   => $row['name'],
            'status' => strtolower($row['status'] ?? 'active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:packaging_types,name',
            'status' => 'required|in:active,inactive',
        ];
    }
}