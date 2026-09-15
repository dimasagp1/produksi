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
    Schema::table('machines', function (Blueprint $table) {
        // Menghapus kolom code
        $table->dropColumn('code');
    });
}

public function down(): void
{
    Schema::table('machines', function (Blueprint $table) {
        // Jaga-jaga jika ingin rollback
        $table->string('code')->unique()->after('name');
    });
}
};
