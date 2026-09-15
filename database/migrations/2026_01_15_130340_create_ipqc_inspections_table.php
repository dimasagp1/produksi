<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ipqc_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('inspection_date');
            $table->time('inspection_time'); // Jam Cek
            // Foreign keys ke data master yang sudah ada
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');

            $table->enum('status', ['ok', 'ng'])->default('ok'); // OK / Not Good
            $table->text('remarks')->nullable(); // Temuan masalah
            $table->string('inspector')->nullable(); // Nama QC
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ipqc_inspections');
    }
};
