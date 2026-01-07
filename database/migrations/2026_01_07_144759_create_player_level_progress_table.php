<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('player_level_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('game_level_id')->constrained('game_levels')->cascadeOnDelete();

            $table->unsignedTinyInteger('best_correct')->default(0); // 0..5
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['player_id', 'game_level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_level_progress');
    }
};
