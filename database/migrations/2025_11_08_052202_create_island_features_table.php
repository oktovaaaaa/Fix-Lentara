<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('island_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('island_id')
                  ->constrained('islands')
                  ->onDelete('cascade'); // kalau island dihapus, fitur ikut hilang

            $table->string('type');      // history, destination, food, culture, dll
            $table->string('title');     // judul fitur
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('order')->nullable(); // urutan tampil

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_features');
    }
};
