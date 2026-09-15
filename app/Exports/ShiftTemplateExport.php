<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShiftTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Shift 4', 'active'],
            ['Shift 5', 'inactive'],
        ];
    }

    public function headings(): array
    {
        return ['name', 'status'];
    }
}