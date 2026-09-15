<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BatchTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Nama Produk (Wajib)',
            'Warna (Wajib)',
            'Target Qty (Wajib)',
            'Tgl Mulai (YYYY-MM-DD)',
            'Deadline (YYYY-MM-DD)',
            'Prioritas (low/medium/high)',
            'Kode Batch (Opsional)',
            'Mesin (Opsional)',
            'Catatan'
        ];
    }
}
