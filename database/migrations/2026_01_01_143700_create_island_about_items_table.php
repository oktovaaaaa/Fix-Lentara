<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('island_about_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')->constrained()->cascadeOnDelete();

            // title opsional
            $table->string('title')->nullable();

            // deskripsi wajib
            $table->text('description');

            // points opsional (multi-line)
            $table->text('points')->nullable();

            // image opsional: simpan PATH / URL
            $table->string('image')->nullable();

            // link opsional: kalau diisi muncul tombol "Selengkapnya"
            $table->string('more_link', 2048)->nullable();

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_about_items');
    }
};
