<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('breakdown_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique(); // BR-2601-001

            $table->foreignId('machine_id')->constrained('machines')->onDelete('cascade');

            $table->dateTime('breakdown_time'); // Waktu kejadian
            $table->dateTime('resolution_time')->nullable(); // Waktu selesai
            $table->integer('downtime_minutes')->default(0); // Durasi (Menit)

            $table->enum('category', ['mechanical', 'electrical', 'software', 'utility', 'other']);
            $table->text('problem_description');
            $table->text('action_taken')->nullable();
            $table->string('technician')->nullable(); // Siapa yang perbaiki

            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('breakdown_reports');
    }
};
