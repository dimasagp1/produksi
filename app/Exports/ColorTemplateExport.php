<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ColorTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Putih', 'active'],
            ['List Gold', 'active'],
            ['Frosted', 'inactive'],
        ];
    }

    public function headings(): array
    {
        return ['name', 'status']; // Harus cocok dengan kolom di database/model
    }
}