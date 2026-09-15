<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique(); // WO-2601-001
            $table->date('report_date');

            $table->foreignId('machine_id')->constrained('machines')->onDelete('cascade');
            $table->string('reported_by'); // Nama Pelapor

            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('issue_description'); // Keluhan

            // Bagian Engineering
            $table->string('assigned_to')->nullable(); // Nama Teknisi
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->text('action_taken')->nullable(); // Tindakan Perbaikan

            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
};
