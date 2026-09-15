<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packaging_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Contoh: Dus Isi 200 Botol
            $table->string('status')->default('active'); // active / inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaging_types');
    }
};