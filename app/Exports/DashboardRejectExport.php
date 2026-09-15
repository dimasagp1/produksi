<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardRejectExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $productId; // Tambahkan properti produk

    public function __construct($startDate, $endDate, $productId = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->productId = $productId; // Simpan produk
    }

    public function collection()
    {
        $query = DB::table('daily_report_rejects')
            ->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')
            ->join('reject_items', 'daily_report_rejects.reject_item_id', '=', 'reject_items.id')
            ->join('products', 'daily_reports.product_id', '=', 'products.id')
            ->join('machines', 'daily_reports.machine_id', '=', 'machines.id')
            ->join('operators', 'daily_reports.operator_id', '=', 'operators.id')
            ->join('coordinators', 'daily_reports.coordinator_id', '=', 'coordinators.id')
            ->join('shifts', 'daily_reports.shift_id', '=', 'shifts.id')
            ->whereBetween('daily_reports.production_date', [$this->startDate, $this->endDate]);

        // TERAPKAN FILTER PRODUK JIKA ADA
        if ($this->productId) {
            $query->where('daily_reports.product_id', $this->productId);
        }

        return $query->select(
            'daily_reports.production_date',
            'shifts.name as shift_name',
            'products.name as product_name',
            'reject_items.name as reject_name',
            'machines.name as machine_name',
            'operators.name as operator_name',
            'coordinators.name as coordinator_name',
            'daily_report_rejects.qty'
        )
            ->orderBy('daily_reports.production_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Shift', 'Produk', 'Jenis Reject', 'Mesin', 'Operator', 'Koordinator', 'Qty Reject'];
    }

    public function map($row): array
    {
        return [
            $row->production_date,
            $row->shift_name,
            $row->product_name,
            $row->reject_name,
            $row->machine_name,
            $row->operator_name,
            $row->coordinator_name,
            $row->qty
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
