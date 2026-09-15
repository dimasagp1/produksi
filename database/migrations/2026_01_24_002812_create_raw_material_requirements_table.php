<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_requirements', function (Blueprint $table) {
            $table->id();

            // Relasi ke Production Plan (MPS)
            $table->foreignId('production_plan_id')
                ->constrained('production_plans')
                ->onDelete('cascade'); // Jika Plan dihapus, kebutuhan material ikut terhapus

            // Relasi ke Raw Material (Bahan Baku)
            // Pastikan nama tabel bahan baku Anda benar (misal: raw_materials atau materials)
            $table->foreignId('raw_material_id')
                ->constrained('raw_materials') // Sesuaikan dengan nama tabel master bahan baku Anda
                ->onDelete('cascade');

            $table->decimal('gross_requirement', 15, 2)->default(0); // Kebutuhan Kotor
            $table->decimal('current_stock', 15, 2)->default(0);     // Stok Saat Ini
            $table->decimal('net_requirement', 15, 2)->default(0);   // Kekurangan (Yang harus dibeli)
            $table->string('unit')->nullable();                      // Satuan (Kg, Liter, Pcs)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_requirements');
    }
};
