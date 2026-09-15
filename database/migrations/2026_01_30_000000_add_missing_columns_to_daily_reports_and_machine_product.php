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
        Schema::table('daily_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_reports', 'actual_cycle_time')) {
                $table->decimal('actual_cycle_time', 8, 2)->default(0)->after('cavity');
            }
            if (!Schema::hasColumn('daily_reports', 'actual_cavity')) {
                $table->integer('actual_cavity')->default(1)->after('actual_cycle_time');
            }
            if (!Schema::hasColumn('daily_reports', 'total_runner')) {
                $table->decimal('total_runner', 8, 2)->default(0)->comment('Total berat runner (kg)')->after('qty_sample');
            }
            if (!Schema::hasColumn('daily_reports', 'purging_kg')) {
                $table->decimal('purging_kg', 10, 3)->default(0)->comment('Input Kg Purging')->after('total_runner');
            }
            if (!Schema::hasColumn('daily_reports', 'weight_per_pcs')) {
                $table->decimal('weight_per_pcs', 10, 3)->default(0)->comment('Berat per Pcs (Gram)')->after('purging_kg');
            }
            if (!Schema::hasColumn('daily_reports', 'wip_previous')) {
                $table->integer('wip_previous')->default(0)->comment('Sisa dari shift sebelumnya')->after('total_counter');
            }
        });

        Schema::table('machine_product', function (Blueprint $table) {
            if (!Schema::hasColumn('machine_product', 'actual_cavity')) {
                $table->integer('actual_cavity')->default(1)->after('cavity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $cols = ['actual_cycle_time', 'actual_cavity', 'total_runner', 'purging_kg', 'weight_per_pcs', 'wip_previous'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('daily_reports', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('machine_product', function (Blueprint $table) {
            if (Schema::hasColumn('machine_product', 'actual_cavity')) {
                $table->dropColumn('actual_cavity');
            }
        });
    }
};
