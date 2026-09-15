<?php

namespace App\Exports;

use App\Models\RejectItem;
use App\Models\Downtime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $rejects;
    protected $downtimes;

    public function __construct()
    {
        // Ambil master data yang aktif
        $this->rejects = RejectItem::where('status', 'active')->orderBy('id')->get();
        $this->downtimes = Downtime::where('status', 'active')->orderBy('id')->get();
    }

    /**
     * Mengembalikan collection kosong (karena ini cuma template)
     * Tapi kita bisa kasih 1 baris contoh data agar user paham cara isinya
     */
    public function collection()
    {
        // Baris Contoh Data (Dummy)
        $example = [
            '2023-12-31',       // Tanggal
            '07:00',            // Jam Mulai
            '15:00',            // Jam Selesai
            'Shift 1',          // Nama Shift
            'Koordinator A',    // Nama Koordinator
            'Operator B',       // Nama Operator
            'Mesin 01',         // Nama Mesin
            'Botol 600ml',      // Nama Produk
            'BATCH-001',        // Kode Batch
            10000,              // Qty Good
            30,                 // Qty Purging
            15.5,               // Cycle Time
            4,                  // Cavity
            'Catatan...'        // Notes
        ];

        // Tambahkan 0 untuk setiap kolom Reject (Contoh)
        foreach ($this->rejects as $reject) {
            $example[] = 0;
        }

        // Tambahkan 0 untuk setiap kolom Downtime (Contoh)
        foreach ($this->downtimes as $dt) {
            $example[] = 0;
        }

        return collect([$example]);
    }

    public function headings(): array
    {
        // 1. Header Utama (Data Laporan)
        $headers = [
            'Tanggal (YYYY-MM-DD)',
            'Jam Mulai (HH:MM)',
            'Jam Selesai (HH:MM)',
            'Nama Shift',
            'Nama Koordinator',
            'Nama Operator',
            'Nama Mesin',
            'Nama Produk',
            'Kode Batch',
            'Qty Good',
            'Qty Purging (Kg)',
            'Cycle Time (Detik)',
            'Cavity',
            'Notes'
        ];

        // 2. Header Dinamis untuk REJECT (Format: "Reject: Nama Item")
        foreach ($this->rejects as $reject) {
            $headers[] = 'Reject: ' . $reject->name;
        }

        // 3. Header Dinamis untuk DOWNTIME (Format: "Downtime: Nama Alasan")
        foreach ($this->downtimes as $dt) {
            $headers[] = 'Downtime: ' . $dt->name;
        }

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold pada baris pertama
            1 => ['font' => ['bold' => true]],
        ];
    }
}
