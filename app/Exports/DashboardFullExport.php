<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class DashboardFullExport implements FromCollection, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();

        // ==========================================
        // 1. HEADER & KOP SURAT
        // ==========================================
        $rows->push(['PT. NAMA PERUSAHAAN ANDA']);
        $rows->push(['Jl. Kawasan Industri No. 123, Semarang, Jawa Tengah - Indonesia']);
        $rows->push(['Telp: (024) 1234567 | Email: produksi@perusahaan.com']);
        $rows->push(['']);

        // Info Laporan
        $rows->push(['EXECUTIVE PRODUCTION DASHBOARD']);
        $rows->push(['Periode: ' . $this->data['startDate']->format('d M Y') . ' - ' . $this->data['endDate']->format('d M Y')]);
        $rows->push(['Dicetak Oleh: ' . $this->data['userName'] . ' | Waktu Cetak: ' . date('d F Y, H:i')]);
        $rows->push(['']);

        // ==========================================
        // 2. KPI SUMMARY (URUTAN DISESUAIKAN DENGAN VIEW)
        // Order: Target -> Achievement -> Output -> Reject -> Yield -> Efisiensi
        // ==========================================
        $rows->push(['RINGKASAN PERFORMA (KPI UTAMA)']);
        $rows->push(['Target Produksi', 'Achievement', 'Total Output', 'Total Reject', 'Avg Yield', 'Efisiensi']);
        $rows->push([
            number_format($this->data['totalTarget'], 0, ',', '.'),
            number_format($this->data['achievement'], 2) . '%', // Pindah ke posisi 2
            number_format($this->data['totalOutput'], 0, ',', '.'),
            number_format($this->data['totalReject'], 0, ',', '.'),
            number_format($this->data['avgYield'], 2) . '%',
            number_format($this->data['avgEfficiency'], 2) . '%'
        ]);
        $rows->push(['']);

        // ==========================================
        // 3. TREN PRODUKSI HARIAN
        // ==========================================
        $rows->push(['A. TREN PRODUKSI HARIAN']);
        $rows->push(['Tanggal', 'Total Output (Pcs)', 'Target (Pcs)']);

        foreach ($this->data['trendOutput'] as $t) {
            $rows->push([
                \Carbon\Carbon::parse($t['date'])->format('d M Y'),
                $t['total'],
                $t['total_target']
            ]);
        }
        $rows->push(['']);

        // ==========================================
        // 4. DETAIL DATA PRODUKSI
        // ==========================================
        $rows->push(['B. DETAIL DATA PRODUKSI PER PRODUK']);
        $rows->push(['No', 'Nama Produk / Tanggal', 'Qty Theory', 'Total Output', 'Qty Good', 'Qty Reject', 'Theo Yield', 'Act Yield', 'Reject %']);

        $no = 1;
        foreach ($this->data['trendTableData'] as $productName => $prodRows) {
            // Kalkulasi Header Produk (Parent)
            $sumTheory = $prodRows->sum('sum_theory');
            $sumOutput = $prodRows->sum('sum_output');
            $sumGood   = $prodRows->sum('sum_good');
            $sumReject = $prodRows->sum('sum_reject');

            $p_theoYield = $sumTheory > 0 ? ($sumOutput / $sumTheory) * 100 : 0;
            $p_actYield  = $sumOutput > 0 ? ($sumGood / $sumOutput) * 100 : 0;
            $p_rejectPct = $sumOutput > 0 ? ($sumReject / $sumOutput) * 100 : 0;

            $rows->push([
                $no++,
                $productName . ' (Total ' . $prodRows->count() . ' Hari)',
                $sumTheory,
                $sumOutput,
                $sumGood,
                $sumReject,
                number_format($p_theoYield, 2) . '%',
                number_format($p_actYield, 2) . '%',
                number_format($p_rejectPct, 2) . '%'
            ]);

            // Detail Harian (Child)
            foreach ($prodRows as $row) {
                $d_theoYield = $row->sum_theory > 0 ? ($row->sum_output / $row->sum_theory) * 100 : 0;
                $d_actYield  = $row->sum_output > 0 ? ($row->sum_good / $row->sum_output) * 100 : 0;
                $d_rejectPct = $row->sum_output > 0 ? ($row->sum_reject / $row->sum_output) * 100 : 0;

                $rows->push([
                    '', // Indent No
                    '   ' . \Carbon\Carbon::parse($row->date)->format('d M Y'),
                    $row->sum_theory,
                    $row->sum_output,
                    $row->sum_good,
                    $row->sum_reject,
                    number_format($d_theoYield, 2) . '%',
                    number_format($d_actYield, 2) . '%',
                    number_format($d_rejectPct, 2) . '%'
                ]);
            }
        }
        $rows->push(['']);

        // JIKA FULL ACCESS
        if ($this->data['isFullAccess']) {

            // C. TOP MESIN
            $rows->push(['C. 10 MESIN & PRODUK TERATAS']);
            $rows->push(['Nama Mesin', 'Nama Produk', 'Total Output']);
            foreach ($this->data['topMachineProducts'] as $mp) {
                $rows->push([$mp->machine_name, $mp->product_name, $mp->total]);
            }
            $rows->push(['']);

            // D. TOP REJECT BY MACHINE
            $rows->push(['D. TOTAL REJECT BERDASARKAN MESIN']);
            $rows->push(['Nama Mesin', 'Total Reject']);
            foreach ($this->data['rejectByMachine'] as $rm) {
                $rows->push([$rm->label, $rm->total]);
            }
            $rows->push(['']);

            // E. OPERATOR PERFORMANCE
            $rows->push(['E. 10 PERINGKAT KINERJA OPERATOR TERATAS']);
            $rows->push(['Rank', 'Nama Operator', 'Target', 'Output', 'Finish Good', 'Output Rate', 'FG Rate']);
            foreach ($this->data['operatorPerformance'] as $index => $op) {
                $rows->push([
                    '#' . ($index + 1),
                    $op->operator_name,
                    $op->total_target,
                    $op->actual_output,
                    $op->actual_fg,
                    number_format($op->output_rate, 2) . '%',
                    number_format($op->fg_rate, 2) . '%'
                ]);
            }
            $rows->push(['']);

            // F. TOP REJECT ISSUES
            $rows->push(['F. 10 ISU KUALITAS TERATAS']);
            $rows->push(['Produk', 'Jenis Reject', 'Mesin', 'Qty Reject', 'Kontribusi (%)']);
            $grandTotalReject = $this->data['totalReject'] > 0 ? $this->data['totalReject'] : 1;

            foreach ($this->data['topRejects'] as $r) {
                $percentage = ($r->total_qty / $grandTotalReject) * 100;
                $rows->push([
                    $r->product_name,
                    $r->reject_name,
                    $r->machine_name,
                    $r->total_qty,
                    number_format($percentage, 2) . '%'
                ]);
            }
            $rows->push(['TOTAL (Top 10)', '', '', $this->data['totalTopRejectsQty'], '']);
            $rows->push(['']);

            // G. TOP DOWNTIME
            $rows->push(['G. 10 DOWNTIME TERATAS']);
            $rows->push(['Nama Mesin', 'Penyebab Masalah', 'Durasi (Menit)', 'Durasi (Jam)']);
            foreach ($this->data['topDowntimes'] as $dt) {
                $rows->push([
                    $dt->machine_name,
                    $dt->downtime_reason,
                    $dt->total_minutes,
                    number_format($dt->total_minutes / 60, 1) . ' Jam'
                ]);
            }
            $rows->push(['TOTAL DURASI', '', $this->data['totalTopDowntimeMinutes'], number_format($this->data['totalTopDowntimeMinutes'] / 60, 1) . ' Jam']);
            $rows->push(['']);

            // H. ANALISIS DISTRIBUSI
            $rows->push(['H. ANALISIS DISTRIBUSI REJECT']);
            $rows->push(['']);

            $renderSubTable = function ($title, $data) use ($rows) {
                $rows->push([$title, '']);
                $rows->push(['Kategori', 'Total Qty']);
                foreach ($data as $item) {
                    $rows->push([$item->label, $item->total]);
                }
                $rows->push(['']);
            };

            $renderSubTable('1. Berdasarkan Jenis Reject', $this->data['rejectByRejectItem']);
            $renderSubTable('2. Berdasarkan Mesin', $this->data['rejectByMachine']);
            $renderSubTable('3. Berdasarkan Operator', $this->data['rejectByOperator']);
            $renderSubTable('4. Berdasarkan Koordinator', $this->data['rejectByCoordinator']);
            $renderSubTable('5. Berdasarkan Shift', $this->data['rejectByShift']);

            // I. DETAIL REJECT TERATAS (URUTAN DISESUAIKAN DENGAN VIEW)
            // Order: No, Tanggal, Shift, Batch, Produk, Jenis Reject, Mesin, Operator, Koordinator, Total, Persentase
            $rows->push(['I. DETAIL DATA REJECT TERATAS']);
            $rows->push(['No', 'Tanggal', 'Shift', 'Batch', 'Produk', 'Jenis Reject', 'Mesin', 'Operator', 'Koordinator', 'Qty', 'Persentase']);
            $no = 1;
            foreach ($this->data['detailRejects'] as $d) {
                $pct = ($d->qty / $grandTotalReject) * 100;
                $rows->push([
                    $no++,
                    \Carbon\Carbon::parse($d->production_date)->format('d/m/Y'),
                    $d->shift_name, // Pindah sebelum batch
                    $d->batch_name,
                    $d->product_name,
                    $d->reject_name,
                    $d->machine_name,
                    $d->operator_name,
                    $d->coordinator_name,
                    $d->qty,
                    number_format($pct, 2) . '%'
                ]);
            }
        }

        // --- 5. FOOTER TANDA TANGAN ---
        $rows->push(['']);
        $rows->push(['']);
        $rows->push(['', '', '', '', '', '', '', '', '', '', 'Semarang, ' . date('d F Y')]);
        $rows->push(['Dibuat Oleh,', '', '', '', 'Diketahui Oleh,', '', '', '', '', '', 'Disetujui Oleh,']);
        $rows->push(['']);
        $rows->push(['']);
        $rows->push(['']);
        $rows->push([
            $this->data['userName'],
            '',
            '',
            '',
            '( ........................... )',
            '',
            '',
            '',
            '',
            '',
            '( ........................... )'
        ]);
        $rows->push([
            'Staff Admin',
            '',
            '',
            '',
            'Manager Produksi',
            '',
            '',
            '',
            '',
            '',
            'Plant Manager'
        ]);

        return $rows;
    }

    public function title(): string
    {
        return 'Laporan Produksi';
    }

    public function styles(Worksheet $sheet)
    {
        // 1. GLOBAL SETTINGS
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Segoe UI');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setShowGridlines(false);

        // 2. KOP SURAT (MERGE A-K karena ada 11 kolom max di Detail Reject)
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Garis Pemisah Kop
        $sheet->getStyle('A4:K4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle('A4:K4')->getBorders()->getBottom()->setColor(new Color('1E3A8A'));

        // 3. JUDUL LAPORAN
        $sheet->mergeCells('A5:K5');
        $sheet->mergeCells('A6:K6');
        $sheet->mergeCells('A7:K7');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('A6:A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 4. LOGIKA STYLING BARIS PER BARIS
        $lastRow = $sheet->getHighestRow();
        $maxCol = 'K';

        $headerColor = '2563EB'; // Blue 600
        $subHeaderColor = 'EFF6FF'; // Blue 50
        $borderColor = 'E2E8F0'; // Slate 200

        for ($row = 9; $row <= $lastRow; $row++) {
            $valA = $sheet->getCell("A$row")->getValue();

            // A. SECTION HEADERS
            if ($valA && (preg_match('/^[A-Z]\./', $valA) || str_contains($valA, 'RINGKASAN') || str_contains($valA, 'ANALISIS'))) {
                $sheet->getStyle("A$row:$maxCol$row")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E3A8A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $subHeaderColor]],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $headerColor]]]
                ]);
                continue;
            }

            // B. TABLE HEADERS (Deteksi berdasarkan isi)
            $headers = [
                'Tanggal',
                'Total Target',
                'Nama Mesin',
                'Rank',
                'Produk',
                'No',
                'Total Output',
                'Kategori'
            ];

            if (in_array($valA, $headers)) {
                $sheet->getStyle("A$row:$maxCol$row")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $headerColor]]]
                ]);
                continue;
            }

            // Sub Header untuk Distribusi
            if ($valA && preg_match('/^[0-9]\./', $valA)) {
                $sheet->getStyle("A$row")->getFont()->setBold(true);
                continue;
            }

            // C. FOOTER TOTAL TABLE
            if (str_contains((string)$valA, 'TOTAL')) {
                $sheet->getStyle("A$row:$maxCol$row")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '9CA3AF']]]
                ]);
                continue;
            }

            // D. ISI DATA (STRIPED ROWS & BORDER)
            $isSignature = in_array($valA, ['Dibuat Oleh,', 'Staff Admin']) || str_contains((string)$sheet->getCell("K$row")->getValue(), 'Semarang');

            if (!empty($valA) && !$isSignature && !str_contains($valA, 'PERIODE') && !str_contains($valA, 'DICETAK')) {
                $sheet->getStyle("A$row:$maxCol$row")->applyFromArray([
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]],
                        'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]],
                        'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]
                    ]
                ]);

                // Highlight Parent Row pada Detail Produk
                if (str_contains((string)$sheet->getCell("B$row")->getValue(), '(Total')) {
                    $sheet->getStyle("A$row:$maxCol$row")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']]
                    ]);
                }
            }
        }

        // 5. STYLING TANDA TANGAN
        $signStart = $lastRow - 7;
        $sheet->getStyle("A$signStart:K$lastRow")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'font' => ['bold' => true]
        ]);

        return [];
    }
}
