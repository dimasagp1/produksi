<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Header COA
        Schema::create('coas', function (Blueprint $table) {
            $table->id();
            $table->string('coa_code')->unique(); // No Sertifikat
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->date('manufacture_date');
            $table->date('expiry_date');
            $table->date('report_date');
            $table->string('approver_name'); // Nama QA Manager
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Tabel Detail Parameter Uji
        Schema::create('coa_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('coas')->onDelete('cascade');
            $table->string('parameter');     // Contoh: pH, Berat Jenis
            $table->string('method')->nullable(); // Contoh: ASTM D1234
            $table->string('specification'); // Contoh: 6.0 - 8.0
            $table->string('result');        // Contoh: 7.2
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coa_items');
        Schema::dropIfExists('coas');
    }
};
