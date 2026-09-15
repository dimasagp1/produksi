<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MachineTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        // Data contoh (dummy) agar user paham cara isinya
        return [
            ['Mesin Injection A1', 'active'],
            ['Mesin Blowing B2', 'inactive'],
        ];
    }

    public function headings(): array
    {
        // Header wajib
        return ['name', 'status'];
    }

    public function styles(Worksheet $sheet)
    {
        // Bold header
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}