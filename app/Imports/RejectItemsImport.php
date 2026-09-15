<?php

namespace App\Imports;

use App\Models\RejectItem;
use App\Models\RejectCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RejectItemsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // 1. Cari Category ID berdasarkan category_code yang diinput di Excel
        $category = RejectCategory::where('code', strtoupper($row['category_code']))->first();

        if (!$category) {
            // Jika kategori tidak ditemukan, lewati atau lempar error validasi
            return null;
        }

        // 2. Simpan Item Reject
        return new RejectItem([
            'name'        => $row['name'],
            'category_id' => $category->id,
            'status'      => strtolower($row['status'] ?? 'active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:reject_items,name',
            'category_code' => 'required',
            'status' => 'required|in:active,inactive',
        ];
    }
}
