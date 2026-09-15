<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // Menambahkan berat standar ke tabel products
    Schema::table('products', function (Blueprint $table) {
        $table->decimal('weight', 10, 3)->default(0)->comment('Berat standar per pcs (Gram)')->after('packaging_qty');
    });

    // Menambahkan qty_sample ke tabel daily_reports
    Schema::table('daily_reports', function (Blueprint $table) {
        $table->integer('qty_sample')->default(0)->comment('Jumlah Sample')->after('qty_reject_total');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('weight');
    });
    Schema::table('daily_reports', function (Blueprint $table) {
        $table->dropColumn('qty_sample');
    });
}
};
