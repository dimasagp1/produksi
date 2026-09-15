<?php

namespace App\Exports;

use App\Models\Downtime;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DowntimesExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        return view('exports.downtimes_excel', [
            'downtimes' => Downtime::orderBy('name')->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1 (Nama PT) - Bold & Ukuran Font Besar 14
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Baris 5 (Header Tabel) - Bold
            5 => ['font' => ['bold' => true]],
        ];
    }
}
