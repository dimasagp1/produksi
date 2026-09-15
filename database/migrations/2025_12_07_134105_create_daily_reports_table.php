<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('daily_reports', function (Blueprint $table) {
        $table->id();
        $table->string('report_code')->unique(); // No Laporan (DR-YYMMDD-XXX)
        $table->date('production_date');
        
        // Relasi Master
        $table->foreignId('batch_id')->constrained();
        $table->foreignId('machine_id')->constrained();
        $table->foreignId('shift_id')->constrained();
        $table->foreignId('coordinator_id')->constrained();
        $table->foreignId('operator_id')->constrained();
        $table->foreignId('product_id')->constrained(); // Redundant tapi perlu untuk query cepat
        $table->foreignId('color_id')->constrained();   // Redundant tapi perlu
        $table->foreignId('packaging_type_id')->nullable()->constrained();

        // Data Teknis (Snapshot)
        $table->decimal('cycle_time', 8, 2);
        $table->integer('cavity');

        // Waktu
        $table->time('start_time');
        $table->time('end_time');
        $table->integer('total_minutes'); // Total jam kerja (menit)

        // Quantity & Output
        $table->integer('qty_theory'); // Target berdasarkan CT
        $table->integer('qty_actual'); // Hasil Mesin (Counter akhir - awal)
        $table->integer('qty_good');   // Barang OK
        $table->integer('qty_reject_total'); // Total Reject
        $table->integer('qty_purging')->default(0); 
        $table->integer('total_output'); // Good + Reject + Purging
        
        // Lain-lain
        $table->integer('total_counter')->nullable(); // Angka di mesin
        $table->integer('wip')->default(0); // Work in Process
        $table->integer('packaging_qty')->default(0); // Jumlah dus/karung
        
        $table->integer('downtime_total')->default(0); // Total menit mati
        $table->decimal('efficiency', 5, 2); // Persentase %
        
        $table->text('notes')->nullable();
        $table->string('status')->default('submitted'); // submitted, approved
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
