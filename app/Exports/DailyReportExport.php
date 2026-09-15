<?php

namespace App\Exports;

use App\Models\DailyReport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportExport implements FromView, WithStyles, WithEvents
{
    protected $query;
    protected $masterRejects;
    protected $masterDowntimes;

    public function __construct($query, $masterRejects, $masterDowntimes)
    {
        $this->query = $query;
        $this->masterRejects = $masterRejects;
        $this->masterDowntimes = $masterDowntimes;
    }

    public function view(): View
    {
        $reports = $this->query->orderBy('production_date', 'desc')->get();

        return view('exports.report_excel', [
            'reports' => $reports,
            'masterRejects' => $this->masterRejects,
            'masterDowntimes' => $this->masterDowntimes
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['name' => 'Arial', 'size' => 10]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Tinggi Baris Header
                $sheet->getRowDimension(6)->setRowHeight(40);

                // 2. Alignment Center & Wrap Text
                $sheet->getStyle($sheet->calculateWorksheetDimension())
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle($sheet->calculateWorksheetDimension())
                    ->getAlignment()->setWrapText(true);

                // 3. ATUR LEBAR KOLOM (ANGKA DIPERBESAR DI SINI)
                $columnWidths = [
                    'A' => 6,  // No
                    'B' => 15, // Tanggal (Lebih lebar)
                    'C' => 10, // Shift
                    'D' => 25, // No Batch (Lebih lebar)
                    'E' => 12, // Prioritas
                    'F' => 50, // Nama Produk (Sangat lebar agar tidak terpotong)
                    'G' => 15, // Warna
                    'H' => 30, // Mesin (Lebih lebar)
                    'I' => 25, // Operator
                    'J' => 25, // Koordinator
                    'K' => 12, // Target
                    'L' => 15, // Jam Kerja
                    'M' => 12, // Total Mnt
                    'N' => 10, // CT Std
                    'O' => 10, // CT Act
                    'P' => 10, // Cav Std
                    'Q' => 10, // Cav Act
                    'R' => 12, // Qty Theory
                    'S' => 12, // Qty Good
                    'T' => 12, // Qty Reject
                    'U' => 15, // Total Output
                    'V' => 12, // Purging
                    'W' => 12, // WIP
                    'X' => 12, // Counter
                    'Y' => 12, // Eff
                    'Z' => 12, // Yield
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // 4. ATUR LEBAR KOLOM DINAMIS (Reject & Downtime)
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // Mulai dari kolom ke-27 (AA) sampai kolom terakhir
                for ($col = 27; $col <= $highestColumnIndex; $col++) {
                    $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

                    // Set lebar kolom detail reject/downtime jadi 30 (sebelumnya 15)
                    $sheet->getColumnDimension($colString)->setWidth(30);

                    // Optional: Center text untuk kolom angka agar rapi
                    // $sheet->getStyle($colString)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
