<?php

namespace App\Imports;

use App\Models\Operator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OperatorsImport implements ToModel, WithHeadingRow, WithValidation

{
    public function model(array $row)
    {
        if (isset($row['name']) && Operator::where('name', $row['name'])->exists()) {

            return null;
        }

        return new Operator([

            'name'   => $row['name'],

            'status' => strtolower($row['status'] ?? 'active'),

        ]);
    }

    public function rules(): array
    {

        return [

            'name' => 'required|unique:operators,name',

            'status' => 'required|in:active,inactive',

        ];
    }
}
