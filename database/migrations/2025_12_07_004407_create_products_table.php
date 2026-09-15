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
    Schema::create('products', function (Blueprint $table) {
        $table->id(); // Ini untuk Nomor/Index
        $table->string('qr_code')->unique(); // QR Code unik
        $table->string('name'); // Nama Produk
        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif'); // Status
        $table->timestamps(); // Created_at & Updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
