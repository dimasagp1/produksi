<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Master Sparepart
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('part_number')->unique(); // Kode Part
            $table->string('name');
            $table->string('location')->nullable(); // Rak/Laci
            $table->string('unit')->default('pcs'); // Pcs, Set, Liter
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(5); // Batas minimum
            $table->timestamps();
        });

        // Tabel Riwayat Transaksi (Masuk/Keluar)
        Schema::create('sparepart_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained('spareparts')->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // Masuk / Keluar
            $table->integer('quantity');
            $table->date('date');

            // Jika dipakai (Out), untuk mesin apa?
            $table->foreignId('machine_id')->nullable()->constrained('machines')->onDelete('set null');

            $table->string('description')->nullable(); // No WO / No PO / Keterangan
            $table->string('pic'); // Siapa yang ambil/input
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sparepart_transactions');
        Schema::dropIfExists('spareparts');
    }
};
