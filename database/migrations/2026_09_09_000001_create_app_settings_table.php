<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, boolean, textarea
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Insert default initial settings
        $now = now();
        DB::table('app_settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'AEJ Manufactra',
                'type' => 'text',
                'group' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_short_name',
                'value' => 'AEJ App',
                'type' => 'text',
                'group' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'company_name',
                'value' => 'PT Abhimata Emas Juara',
                'type' => 'text',
                'group' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Unified System',
                'type' => 'text',
                'group' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_sub_tagline',
                'value' => 'Production System',
                'type' => 'text',
                'group' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem ERP manufaktur terintegrasi untuk efisiensi produksi dan pemantauan real-time.',
                'type' => 'textarea',
                'group' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'footer_text',
                'value' => 'PT Abhimata Emas Juara. All rights reserved.',
                'type' => 'text',
                'group' => 'general',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
