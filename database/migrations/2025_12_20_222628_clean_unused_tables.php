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
    // Menghapus tabel Cache & Lock
    Schema::dropIfExists('cache');
    Schema::dropIfExists('cache_locks');

    // Menghapus tabel Jobs/Queue
    Schema::dropIfExists('jobs');
    Schema::dropIfExists('job_batches');
    Schema::dropIfExists('failed_jobs');

    // Menghapus tabel Password Reset (Jika fitur lupa password tidak dipakai)
    Schema::dropIfExists('password_reset_tokens');

}

public function down(): void
{
    // Kosongkan saja, karena kita tidak berniat mengembalikan tabel ini
}
};
