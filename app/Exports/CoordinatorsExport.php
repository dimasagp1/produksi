<?php

namespace App\Exports;

use App\Models\Coordinator;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoordinatorsExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        // Memanggil file view: resources/views/exports/coordinators_excel.blade.php
        return view('exports.coordinators_excel', [
            'coordinators' => Coordinator::orderBy('name')->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Nama PT) - Bold & Ukuran Font Besar 14
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Baris 5 (Header Tabel: No, Nama, Status) - Bold
            5 => ['font' => ['bold' => true]],
        ];
    }
}
