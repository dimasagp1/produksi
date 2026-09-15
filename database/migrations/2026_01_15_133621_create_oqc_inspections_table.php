<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oqc_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('inspection_date');
            $table->time('inspection_time');

            // Relasi ke Batch Produksi
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->integer('sample_size')->default(0); // Jumlah Sampel di cek
            $table->integer('defect_qty')->default(0);  // Jumlah Temuan Cacat

            // Poin Pengecekan Khusus OQC
            $table->enum('packaging_status', ['ok', 'ng'])->default('ok'); // Cek Kemasan
            $table->enum('labeling_status', ['ok', 'ng'])->default('ok'); // Cek Label

            $table->enum('status', ['pass', 'reject', 'hold'])->default('pass'); // Keputusan Akhir

            $table->text('remarks')->nullable();
            $table->string('inspector')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('oqc_inspections');
    }
};
