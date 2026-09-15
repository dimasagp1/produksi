<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Master Work Center (Pos Kerja / Mesin Group)
        Schema::create('work_centers', function (Blueprint $table) {
            $table->id();
            $table->string('wc_code')->unique(); // WC-01
            $table->string('name'); // Misal: Mesin Injection, Meja Assembly
            $table->decimal('cost_per_hour', 15, 2)->default(0); // Biaya per jam (listrik+sdm)
            $table->timestamps();
        });

        // 2. Routing (Urutan Proses)
        Schema::create('product_routings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('work_center_id')->constrained('work_centers')->onDelete('cascade');

            $table->integer('step_number'); // Urutan: 10, 20, 30...
            $table->string('operation_name'); // Nama Proses: "Cetakan Awal"
            $table->decimal('standard_time', 10, 2)->default(0); // Waktu standar (menit/detik)
            $table->string('time_unit')->default('minutes'); // seconds, minutes, hours

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_routings');
        Schema::dropIfExists('work_centers');
    }
};
