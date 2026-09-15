<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('capa_actions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: CPA-2601-001
            $table->date('issue_date');
            $table->string('source'); // Sumber: Komplain Customer, Audit, Temuan Internal
            $table->text('problem_description');
            $table->text('root_cause')->nullable(); // Analisa Akar Masalah
            $table->text('corrective_action')->nullable(); // Tindakan Perbaikan
            $table->text('preventive_action')->nullable(); // Tindakan Pencegahan
            $table->string('pic'); // Penanggung Jawab
            $table->date('due_date')->nullable(); // Batas Waktu
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('capa_actions');
    }
};
