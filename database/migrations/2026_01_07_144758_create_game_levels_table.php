<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('island_id')->constrained('islands')->cascadeOnDelete();

            $table->string('title'); // contoh: "Level 1", atau nama bebas
            $table->unsignedInteger('order')->default(1);
            $table->boolean('is_active')->default(true);

            // ✅ OPTIONAL TIME LIMIT (0 = tidak ada limit)
            $table->unsignedInteger('time_limit_seconds')->default(0);

            $table->timestamps();
            $table->index(['island_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_levels');
    }
};
