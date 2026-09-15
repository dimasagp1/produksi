<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packaging_types', function (Blueprint $table) {
            // Kolom numerik yang menyimpan faktor konversi (400)
            $table->decimal('conversion_quantity', 8, 2)->default(1)->after('name'); 
            // Kolom opsional untuk unit yang dikemas (Botol, Tutup, Preform)
            $table->string('content_unit')->nullable()->after('conversion_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('packaging_types', function (Blueprint $table) {
            $table->dropColumn('conversion_quantity');
            $table->dropColumn('content_unit');
        });
    }
};