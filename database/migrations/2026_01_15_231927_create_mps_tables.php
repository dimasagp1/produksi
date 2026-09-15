<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Header Rencana Bulanan
        Schema::create('production_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_code')->unique(); // MPS-2026-01
            $table->date('period'); // Periode (Bulan/Tahun)
            $table->enum('status', ['draft', 'confirmed', 'closed'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Detail Per Produk
        Schema::create('production_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_plan_id')->constrained('production_plans')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->integer('beginning_stock')->default(0); // Stok Awal Bulan
            $table->integer('sales_forecast')->default(0);  // Rencana Jual
            $table->integer('production_qty')->default(0);  // Rencana Produksi (MPS)
            $table->integer('ending_stock')->default(0);    // Estimasi Stok Akhir

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_plan_items');
        Schema::dropIfExists('production_plans');
    }
};
