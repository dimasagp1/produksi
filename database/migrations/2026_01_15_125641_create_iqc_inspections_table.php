<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('iqc_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('inspection_date');
            $table->string('material_name'); // Nama Bahan Baku
            $table->string('supplier_name')->nullable(); // Supplier
            $table->string('batch_no')->nullable(); // No Batch Kedatangan
            $table->decimal('qty_received', 10, 2); // Jumlah Datang
            $table->decimal('qty_rejected', 10, 2)->default(0); // Jumlah Reject
            $table->enum('status', ['approved', 'rejected', 'hold'])->default('approved'); // Status Akhir
            $table->text('remarks')->nullable(); // Catatan QC
            $table->string('inspector')->nullable(); // Nama Pemeriksa
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('iqc_inspections');
    }
};
