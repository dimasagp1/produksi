<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CoordinatorTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        // Data Koordinator awal yang diminta
        return [
            ['Alip', 'active'],
            ['Hanifan', 'active'],
            ['Alvin', 'active'],
        ];
    }

    public function headings(): array
    {
        return ['name', 'status'];
    }
}