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
        // 1. Tabel Utama (Header) - Edit file create_daily_reports_table.php Anda dengan ini:
        // (Sebenarnya bisa digabung, tapi biar rapi saya tulis strukturnya di sini)

        // 2. Tabel Detail Reject
        Schema::create('daily_report_rejects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('reject_item_id')->constrained('reject_items');
            $table->integer('qty'); // Jumlah reject per jenis
            $table->timestamps();
        });

        // 3. Tabel Detail Downtime
        Schema::create('daily_report_downtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('downtime_id')->constrained('downtimes'); // Alasan downtime
            $table->integer('duration'); // Durasi dalam menit
            $table->text('remarks')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_details_tables');
    }
};
