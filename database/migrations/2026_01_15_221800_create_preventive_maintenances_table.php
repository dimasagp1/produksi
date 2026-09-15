<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('preventive_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained('machines')->onDelete('cascade');
            $table->string('task_name'); // Nama Aktivitas (misal: Ganti Oli)
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);

            $table->date('last_maintenance_date')->nullable(); // Terakhir dikerjakan
            $table->date('next_due_date'); // Jadwal berikutnya

            $table->string('assigned_to')->nullable(); // Teknisi PJ
            $table->text('description')->nullable(); // Detail cara kerja
            $table->enum('status', ['scheduled', 'overdue'])->default('scheduled'); // Status sistem

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preventive_maintenances');
    }
};
