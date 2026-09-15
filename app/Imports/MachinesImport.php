<?php

namespace App\Imports;

use App\Models\Machine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class MachinesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['name'])) {
            return null;
        }

        // Normalisasi status (agar user bisa tulis 'Ready', 'Active', 'On', dll)
        $inputStatus = strtolower($row['status'] ?? '');
        $finalStatus = 'active'; // Default

        if (in_array($inputStatus, ['inactive', 'off', 'maintenance', 'rusak', 'nonaktif'])) {
            $finalStatus = 'inactive';
        }

        return new Machine([
            'name'   => $row['name'],
            'status' => $finalStatus,
        ]);
    }
}