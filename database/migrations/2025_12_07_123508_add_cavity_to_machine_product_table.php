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
    Schema::table('machine_product', function (Blueprint $table) {
        // Menambahkan kolom cavity setelah cycle_time, default 1
        $table->integer('cavity')->default(1)->after('cycle_time');
    });
}

public function down(): void
{
    Schema::table('machine_product', function (Blueprint $table) {
        $table->dropColumn('cavity');
    });
}
};
