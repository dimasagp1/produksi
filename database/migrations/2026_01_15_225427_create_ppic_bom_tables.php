<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Master Bahan Baku
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_code')->unique(); // RM-001
            $table->string('name');
            $table->string('unit'); // kg, liter, pcs
            $table->decimal('std_cost', 15, 2)->default(0); // Harga standar per unit
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // 2. Struktur BOM (Pivot Table)
        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->decimal('quantity', 10, 4); // Kebutuhan per 1 unit produk
            $table->string('unit'); // Satuan pakai (bisa beda dengan satuan stok, tapi kita samakan dulu)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bill_of_materials');
        Schema::dropIfExists('raw_materials');
    }
};
