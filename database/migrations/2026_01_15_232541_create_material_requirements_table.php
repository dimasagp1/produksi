<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('material_requirements', function (Blueprint $table) {
            $table->id();
            // Terhubung ke Periode MPS tertentu
            $table->foreignId('production_plan_id')->constrained('production_plans')->onDelete('cascade');

            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');

            $table->decimal('gross_requirement', 15, 4); // Total Kebutuhan Produksi
            $table->integer('current_stock'); // Stok Saat Ini
            $table->decimal('net_requirement', 15, 4); // Yang Harus Dibeli (Gross - Stok)
            $table->string('unit');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_requirements');
    }
};
