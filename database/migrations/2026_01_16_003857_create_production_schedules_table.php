<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('production_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('spk_number')->unique(); // SPK-2601-001

            // Link ke Rencana Bulanan (MPS)
            $table->foreignId('production_plan_id')->constrained('production_plans')->onDelete('cascade');

            // Produk apa yang dibuat
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Di Mesin mana (Work Center)
            $table->foreignId('work_center_id')->constrained('work_centers')->onDelete('cascade');

            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('quantity'); // Target Qty per SPK ini

            $table->string('color')->default('#3b82f6'); // Warna Bar di Board
            $table->enum('status', ['planned', 'running', 'completed'])->default('planned');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_schedules');
    }
};
