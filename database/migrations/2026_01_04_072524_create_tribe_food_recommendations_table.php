<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tribe_food_recommendations', function (Blueprint $table) {
            $table->id();

            $table->string('tribe_key', 120);      // contoh: Batak
            $table->string('week_key', 20);        // contoh: 2026-W01
            $table->string('region_slug', 120)->nullable(); // contoh: sumatera (opsional)
            $table->json('payload');               // isi items[] lengkap
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->unique(['tribe_key', 'week_key']);
            $table->index(['tribe_key']);
            $table->index(['week_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tribe_food_recommendations');
    }
};
