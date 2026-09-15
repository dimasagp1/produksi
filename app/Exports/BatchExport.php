<?php

namespace App\Exports;

use App\Models\Batch;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class BatchExport implements FromView, WithColumnWidths, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        // Tambahkan 'reports' untuk eager loading perhitungan data
        $query = Batch::with(['product', 'color', 'machine', 'reports']);

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_code', 'like', "%$search%")
                    ->orWhereHas('product', fn($sq) => $sq->where('name', 'like', "%$search%"));
            });
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        $batches = $query->orderByRaw("FIELD(status, 'running', 'planning', 'hold', 'completed', 'canceled')")
            ->orderBy('start_date', 'desc')
            ->get();

        return view('exports.batches_excel', [
            'batches' => $batches
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 22,  // Kode Batch
            'C' => 12,  // Status
            'D' => 10,  // Prioritas
            'E' => 30,  // Nama Produk
            'F' => 15,  // Warna
            'G' => 18,  // Mesin
            'H' => 12,  // Target
            'I' => 12,  // Output (Current)
            'J' => 12,  // Sisa (Remaining)
            'K' => 12,  // FG
            'L' => 12,  // Reject Qty
            'M' => 10,  // Reject %
            'N' => 12,  // WIP
            'O' => 12,  // Counter
            'P' => 12,  // Sample
            'Q' => 12,  // Downtime
            'R' => 10,  // Avg Eff
            'S' => 15,  // Tgl Mulai
            'T' => 15,  // Deadline
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling Header (Baris 6 adalah header tabel)
        $sheet->getRowDimension(6)->setRowHeight(35); // Sedikit lebih tinggi

        // Style Global
        $sheet->getStyle('A:T')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A:T')->getAlignment()->setWrapText(true);

        // Alignment Center
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center'); // No
        $sheet->getStyle('C:D')->getAlignment()->setHorizontal('center'); // Status, Prio
        $sheet->getStyle('H:T')->getAlignment()->setHorizontal('center'); // Angka-angka & Tanggal

        // Border untuk seluruh tabel data (mulai baris 6 sampai data terakhir)
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A6:T' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}
