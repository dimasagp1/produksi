<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            // Relasi ke Mesin (Work Center)
            $table->foreignId('work_center_id')->nullable()->constrained('work_centers')->onDelete('set null');
            
            // Waktu Rencana Produksi
            $table->dateTime('planned_start')->nullable();
            $table->dateTime('planned_end')->nullable();
            
            // Warna untuk visualisasi (Opsional)
            $table->string('visual_color')->default('#3b82f6');
        });
    }

    public function down()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['work_center_id']);
            $table->dropColumn(['work_center_id', 'planned_start', 'planned_end', 'visual_color']);
        });
    }
};