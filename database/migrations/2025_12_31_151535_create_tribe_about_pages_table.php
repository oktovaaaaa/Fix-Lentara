<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tribe_about_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')->constrained()->cascadeOnDelete();
            $table->string('tribe_key', 120);

            $table->string('label_small', 180)->nullable();
            $table->string('hero_title', 255)->nullable();
            $table->text('hero_description')->nullable();

            $table->string('more_link', 2048)->nullable();

            $table->timestamps();

            $table->unique(['island_id', 'tribe_key']);
            $table->index(['island_id', 'tribe_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tribe_about_pages');
    }
};
    