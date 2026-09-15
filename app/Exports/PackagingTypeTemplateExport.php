<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PackagingTypeTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Dus Karton A1 (Isi 200 Botol)', 'active'],
            ['Bag Plastik Besar (Isi 1000)', 'active'],
        ];
    }

    public function headings(): array
    {
        return ['name', 'status'];
    }
}