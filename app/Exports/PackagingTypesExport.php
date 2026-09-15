<?php

namespace App\Exports;

use App\Models\PackagingType;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PackagingTypesExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        return view('exports.packaging_types_excel', [
            'packagingTypes' => PackagingType::orderBy('name')->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true]],
        ];
    }
}
