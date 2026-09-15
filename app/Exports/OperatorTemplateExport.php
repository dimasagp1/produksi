<?php

namespace App\Exports;



use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;



class OperatorTemplateExport implements FromArray, WithHeadings, ShouldAutoSize

{

    public function array(): array

    {

        return [

            ['Slamet Riyadi', 'active'],

            ['Wahyu Hidayat', 'inactive'],

        ];
    }



    public function headings(): array

    {

        return ['name', 'status'];
    }
}
