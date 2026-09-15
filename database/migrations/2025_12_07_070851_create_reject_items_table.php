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
        Schema::create('reject_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('reject_categories')->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reject_items');
    }
};
