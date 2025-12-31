<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tribe_about_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')->constrained()->cascadeOnDelete();
            $table->string('tribe_key', 120);

            // point opsional
$table->text('points')->nullable();
            // title opsional
            $table->string('title')->nullable();

            // deskripsi wajib (sesuai request kamu)
            $table->text('description');

            // image opsional: simpan PATH hasil upload, bukan URL input
            $table->string('image')->nullable();

            // link opsional untuk tombol Selengkapnya di item
            $table->string('more_link', 2048)->nullable();

            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['island_id', 'tribe_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tribe_about_items');
    }
};
