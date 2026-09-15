<?php

namespace App\Imports;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Color;
use App\Models\Machine;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class BatchImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cari Product ID berdasarkan Nama
        $product = Product::where('name', 'like', '%' . $row['nama_produk_wajib'] . '%')->first();

        // 2. Cari Color ID
        $color = Color::where('name', 'like', '%' . $row['warna_wajib'] . '%')->first();

        // 3. Cari Machine ID (Opsional)
        $machine = null;
        if (!empty($row['mesin_opsional'])) {
            $machine = Machine::where('name', 'like', '%' . $row['mesin_opsional'] . '%')->first();
        }

        // Jika Produk/Warna tidak ditemukan, skip baris ini (atau bisa throw error)
        if (!$product || !$color) {
            return null;
        }

        // 4. Auto Generate Batch Code jika kosong
        $batchCode = $row['kode_batch_opsional'];
        if (empty($batchCode)) {
            $today = date('dmy');
            $latestBatch = Batch::where('batch_code', 'like', 'BCH-' . $today . '%')->count();
            $number = str_pad($latestBatch + 1 + rand(1, 100), 3, '0', STR_PAD_LEFT); // Rand untuk menghindari duplikat saat bulk import
            $batchCode = 'BCH-' . $today . '-' . $number;
        }

        // 5. Create Data
        return new Batch([
            'batch_code'      => $batchCode,
            'product_id'      => $product->id,
            'color_id'        => $color->id,
            'machine_id'      => $machine ? $machine->id : null,
            'target_quantity' => $row['target_qty_wajib'] ?? 0,
            'current_quantity' => 0,
            'start_date'      => $this->transformDate($row['tgl_mulai_yyyy_mm_dd']),
            'deadline_date'   => !empty($row['deadline_yyyy_mm_dd']) ? $this->transformDate($row['deadline_yyyy_mm_dd']) : null,
            'priority'        => strtolower($row['prioritas_lowmediumhigh'] ?? 'medium'),
            'status'          => 'planning',
            'notes'           => $row['catatan'] ?? null,
        ]);
    }

    // Helper untuk konversi tanggal Excel
    private function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format($format);
        }
    }
}
