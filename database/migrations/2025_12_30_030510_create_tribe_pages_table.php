<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tribe_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')->constrained('islands')->cascadeOnDelete();

            // key suku disamakan dengan config tribes.php (contoh: "Aceh", "Batak")
            $table->string('tribe_key');

            // Title besar + deskripsi besar (seperti header “Peta Interaktif Nusantara”)
            $table->string('hero_title')->nullable();
            $table->text('hero_description')->nullable();

            // optional kalau nanti kamu mau header image
            $table->string('hero_image')->nullable();

            $table->timestamps();

            $table->unique(['island_id', 'tribe_key']);
            $table->index(['island_id', 'tribe_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tribe_pages');
    }
};
