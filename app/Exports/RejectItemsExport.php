<?php

namespace App\Exports;

use App\Models\RejectItem;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RejectItemsExport implements FromView, ShouldAutoSize, WithStyles
{
    public function view(): View
    {
        return view('exports.reject_items_excel', [
            'rejectItems' => RejectItem::with('category')->orderBy('name')->get()
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
