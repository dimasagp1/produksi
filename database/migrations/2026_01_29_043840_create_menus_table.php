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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Nama Label Menu (misal: Laporan)
            $table->string('route')->nullable(); // Nama Route Laravel (misal: reports.index)
            $table->string('icon')->nullable();  // Class FontAwesome (misal: fas fa-file)
            $table->integer('parent_id')->nullable(); // Untuk Submenu (0 atau null jika menu utama)
            $table->integer('order')->default(0); // Urutan menu
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
