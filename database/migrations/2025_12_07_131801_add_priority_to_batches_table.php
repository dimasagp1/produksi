<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('batches', function (Blueprint $table) {
        // Menambah kolom prioritas
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('status');
    });
}

public function down(): void
{
    Schema::table('batches', function (Blueprint $table) {
        $table->dropColumn('priority');
    });
}
};
