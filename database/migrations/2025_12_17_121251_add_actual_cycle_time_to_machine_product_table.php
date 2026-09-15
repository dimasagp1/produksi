<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('machine_product', function (Blueprint $table) {
            // Tambahkan kolom actual_cycle_time setelah cycle_time
            $table->decimal('actual_cycle_time', 8, 2)->default(0)->after('cycle_time');
        });
    }

    public function down()
    {
        Schema::table('machine_product', function (Blueprint $table) {
            $table->dropColumn('actual_cycle_time');
        });
    }
};