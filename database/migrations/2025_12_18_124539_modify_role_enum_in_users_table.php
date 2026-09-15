<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Menambahkan semua role yang digunakan oleh sistem
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'gm', 'manager', 'spv', 'leader', 'operator') NOT NULL DEFAULT 'leader'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'leader', 'manager') NOT NULL DEFAULT 'leader'");
    }
};