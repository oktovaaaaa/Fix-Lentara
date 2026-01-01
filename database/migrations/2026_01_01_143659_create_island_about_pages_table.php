<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('island_about_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')->constrained()->cascadeOnDelete();

            // Header sekali per pulau
            $table->string('label_small')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('more_link', 2048)->nullable();

            $table->timestamps();

            // optional: biar 1 pulau cuma 1 header
            $table->unique('island_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_about_pages');
    }
};
