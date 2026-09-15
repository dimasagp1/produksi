<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DowntimeTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        // Data alasan downtime umum
        return [
            ['Breakdown / Failure', 'active'],
            ['Setup & Changeover', 'active'],
            ['Lack of Material', 'active'],
        ];
    }

    public function headings(): array
    {
        return ['name', 'status'];
    }
}