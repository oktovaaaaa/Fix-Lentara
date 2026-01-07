<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('heritage_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')->constrained('islands')->cascadeOnDelete();

            // key suku disamakan dengan config tribes.php (contoh: "Aceh", "Batak")
            $table->string('tribe_key');

            // kategori: pakaian / rumah_tradisi / senjata_alatmusik
            $table->string('category');

            $table->string('title');
            $table->text('description')->nullable();

            // ✅ BARU (opsional)
            // lokasi singkat (contoh: "Banda Aceh", "Toraja, Sulawesi Selatan")
            $table->string('location')->nullable();

            // url untuk detail (wiki/artikel), opsional
            $table->string('detail_url', 2048)->nullable();

            $table->string('image_path')->nullable();

            // opsional untuk urut
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['island_id', 'tribe_key', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heritage_items');
    }
};
