<?php

namespace App\Exports;

use App\Models\Operator;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OperatorsExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        return view('exports.operators_excel', [
            'operators' => Operator::orderBy('name')->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Nama PT) - Bold & Besar
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Baris 5 (Header Tabel) - Bold
            5 => ['font' => ['bold' => true]],
        ];
    }
}
