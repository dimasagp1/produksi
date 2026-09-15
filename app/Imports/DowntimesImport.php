<?php
namespace App\Imports;

use App\Models\Downtime;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DowntimesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        if (isset($row['name']) && Downtime::where('name', $row['name'])->exists()) {
            return null; 
        }

        return new Downtime([
            'name'   => $row['name'],
            'status' => strtolower($row['status'] ?? 'active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:downtimes,name',
            'status' => 'required|in:active,inactive',
        ];
    }
}