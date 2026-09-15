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
    Schema::create('batches', function (Blueprint $table) {
        $table->id();
        $table->string('batch_code')->unique(); // Kode Batch (Manual/Auto)
        
        // Relasi ke Master Data
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->foreignId('color_id')->constrained()->onDelete('cascade');
        // Opsional: Langsung assign mesin
        $table->foreignId('machine_id')->nullable()->constrained()->onDelete('set null'); 

        // Data Produksi
        $table->integer('target_quantity');
        $table->integer('current_quantity')->default(0); // Progress saat ini
        $table->integer('reject_quantity')->default(0);  // Total reject
        
        // Waktu
        $table->date('start_date');
        $table->date('deadline_date')->nullable(); // Saran tambahan

        // Status
        $table->enum('status', ['planning', 'running', 'hold', 'completed', 'canceled'])->default('planning');
        
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
