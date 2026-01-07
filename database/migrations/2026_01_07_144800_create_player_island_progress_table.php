<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('player_island_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('island_id')->constrained('islands')->cascadeOnDelete();

            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['player_id', 'island_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_island_progress');
    }
};
