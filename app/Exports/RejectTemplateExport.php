<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RejectTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Bubble', 'RECYCLE', 'active'],
            ['Black Spot', 'GA', 'active'],
        ];
    }

    public function headings(): array
    {
        // Catatan: category_code harus sesuai dengan kolom 'code' di tabel reject_categories
        return ['name', 'category_code', 'status']; 
    }
}