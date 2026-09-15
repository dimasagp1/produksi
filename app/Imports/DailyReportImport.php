<?php

namespace App\Imports;

use App\Models\DailyReport;
use App\Models\RejectItem;
use App\Models\Downtime;
use App\Models\Batch;
use App\Models\Shift;
use App\Models\Machine;
use App\Models\Product;
use App\Models\Coordinator;
use App\Models\Operator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <--- TAMBAHKAN INI
use Carbon\Carbon;

class DailyReportImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    // Cache Master Data agar tidak query berulang-ulang dalam loop
    protected $rejects;
    protected $downtimes;
    protected $shifts;
    protected $machines;
    protected $products;
    protected $coordinators;
    protected $operators;

    public function __construct()
    {
        $this->rejects = RejectItem::all()->pluck('id', 'name'); // Key: Name, Value: ID
        $this->downtimes = Downtime::all()->pluck('id', 'name');
        $this->shifts = Shift::all()->pluck('id', 'name');
        $this->machines = Machine::all()->pluck('id', 'name');
        $this->products = Product::all()->pluck('id', 'name');
        $this->coordinators = Coordinator::all()->pluck('id', 'name');
        $this->operators = Operator::all()->pluck('id', 'name');
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Validasi Baris Kosong (Jika tanggal kosong, skip)
        if (!isset($row['tanggal_yyyy_mm_dd']) || empty($row['tanggal_yyyy_mm_dd'])) {
            return null;
        }

        // 2. Cari ID Master Data berdasarkan Nama di Excel
        // Gunakan strtolower/trim agar pencarian tidak case-sensitive
        // NOTE: Pastikan nama di Excel SAMA PERSIS dengan di database
        $shiftId = $this->getIdByName($this->shifts, $row['nama_shift']);
        $machineId = $this->getIdByName($this->machines, $row['nama_mesin']);
        $productId = $this->getIdByName($this->products, $row['nama_produk']);
        $coordId = $this->getIdByName($this->coordinators, $row['nama_koordinator']);
        $operatorId = $this->getIdByName($this->operators, $row['nama_operator']);

        // Cari Batch ID berdasarkan Kode Batch
        $batch = Batch::where('batch_code', $row['kode_batch'])->first();
        $batchId = $batch ? $batch->id : null;

        // 3. Hitung Waktu & Kalkulasi
        try {
            // Handle format tanggal excel (integer) atau string
            $prodDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_yyyy_mm_dd'])->format('Y-m-d');
        } catch (\Exception $e) {
            $prodDate = $row['tanggal_yyyy_mm_dd']; // Fallback jika string biasa
        }

        $startTime = $row['jam_mulai_hhmm'] ?? '00:00';
        $endTime = $row['jam_selesai_hhmm'] ?? '00:00';

        $start = Carbon::parse($prodDate . ' ' . $startTime);
        $end = Carbon::parse($prodDate . ' ' . $endTime);
        if ($end->lessThan($start)) $end->addDay(); // Shift malam

        $totalMinutes = $end->diffInMinutes($start);

        // 4. Proses Loop Kolom Reject & Downtime dari Row Excel
        $totalRejectQty = 0;
        $totalDowntimeDur = 0;
        $rejectData = [];
        $downtimeData = [];

        // HAPUS LOOP FOREACH $row DI SINI (TIDAK EFISIEN & DOUBLE LOGIC)
        // Kita langsung loop master data saja di bawah (Strategi Import Dinamis)

        /**
         * STRATEGI IMPORT DINAMIS:
         * Karena $row di ToModel sudah diubah keys-nya menjadi slug (lowercase),
         * kita akan meloop Master Data kita dan mencoba mencocokkan key-nya.
         */

        // A. Proses Rejects
        foreach ($this->rejects as $name => $id) {
            // Convert nama DB ke Slug Excel (contoh: "Black Spot" -> "reject_black_spot")
            // PERBAIKAN: Gunakan Str::slug (tanpa backslash karena sudah di use)
            $slug = 'reject_' . Str::slug($name, '_');

            if (isset($row[$slug]) && $row[$slug] > 0) {
                $qty = (float) $row[$slug];
                $totalRejectQty += $qty;
                $rejectData[] = ['reject_item_id' => $id, 'qty' => $qty];
            }
        }

        // B. Proses Downtimes
        foreach ($this->downtimes as $name => $id) {
            // PERBAIKAN: Gunakan Str::slug
            $slug = 'downtime_' . Str::slug($name, '_');

            if (isset($row[$slug]) && $row[$slug] > 0) {
                $dur = (float) $row[$slug];
                $totalDowntimeDur += $dur;
                $downtimeData[] = ['downtime_id' => $id, 'duration' => $dur];
            }
        }

        // 5. Hitung Final (Efficiency, Yield)
        $qtyGood = (float) ($row['qty_good'] ?? 0);
        $ct = (float) ($row['cycle_time_detik'] ?? 0);
        $cavity = (int) ($row['cavity'] ?? 1);

        $effectiveMinutes = $totalMinutes - $totalDowntimeDur;
        $qtyTheory = 0;
        if ($ct > 0 && $effectiveMinutes > 0) {
            $qtyTheory = floor(($effectiveMinutes * 60 / $ct) * $cavity);
        }

        $totalOutput = $qtyGood + $totalRejectQty;
        $yield = ($totalOutput > 0) ? ($qtyGood / $totalOutput) * 100 : 0;
        $efficiency = ($qtyTheory > 0) ? ($qtyGood / $qtyTheory) * 100 : 0;

        // 6. Simpan Header Laporan
        $report = DailyReport::create([
            'report_code' => 'DR-IMP-' . time() . '-' . rand(100, 999),
            'production_date' => $prodDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_minutes' => $totalMinutes,
            'shift_id' => $shiftId,
            'coordinator_id' => $coordId,
            'operator_id' => $operatorId,
            'machine_id' => $machineId,
            'product_id' => $productId,
            'batch_id' => $batchId,
            'cycle_time' => $ct,
            'cavity' => $cavity,
            'qty_good' => $qtyGood,
            'qty_reject_total' => $totalRejectQty,
            'qty_purging' => $row['qty_purging_kg'] ?? 0,
            'downtime_total' => $totalDowntimeDur,
            'qty_theory' => $qtyTheory,
            'total_output' => $totalOutput,
            'qty_actual' => $totalOutput, // Sama dengan total output
            'efficiency' => $efficiency,
            'yield' => $yield,
            'notes' => $row['notes'] ?? 'Imported via Excel',
            'status' => 'approved' // Auto approve jika import
        ]);

        // 7. Simpan Relasi (Child)
        foreach ($rejectData as $r) {
            $report->rejects()->create($r);
        }
        foreach ($downtimeData as $d) {
            $report->downtimes()->create($d);
        }

        // 8. Update Progress Batch (Optional)
        if ($batchId) {
            $b = Batch::find($batchId);
            if ($b) {
                $b->current_quantity += $qtyGood;
                $b->reject_quantity += $totalRejectQty;
                $b->save();
            }
        }

        return $report;
    }

    // Helper untuk mencari ID dari Collection pluck
    private function getIdByName($collection, $name)
    {
        // Cari key yang cocok case-insensitive
        $name = trim(strtolower($name));
        foreach ($collection as $key => $val) {
            if (trim(strtolower($key)) == $name) {
                return $val;
            }
        }
        return null;
    }
}
