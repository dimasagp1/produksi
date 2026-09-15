<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('capacity_plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_plan_id')->constrained('production_plans')->onDelete('cascade');
            $table->foreignId('work_center_id')->constrained('work_centers')->onDelete('cascade');

            $table->decimal('required_hours', 15, 2); // Beban (Load)
            $table->decimal('available_hours', 15, 2); // Kapasitas Tersedia
            $table->decimal('utilization_percentage', 8, 2); // Persentase %
            $table->enum('status', ['underload', 'optimal', 'overload'])->default('optimal');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('capacity_plannings');
    }
};
