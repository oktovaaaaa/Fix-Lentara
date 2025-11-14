<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('islands', function (Blueprint $table) {
        $table->id();
        $table->string('name');              // Contoh: "Pulau Sumatera"
        $table->string('slug')->unique();    // Contoh: "sumatera"
        $table->string('place_label');       // Contoh: "Danau Toba", "Masyarakat Bali"
        $table->string('title');             // Contoh yang muncul besar: "PULAU"
        $table->string('subtitle');          // Contoh: "SUMATERA"
        $table->text('short_description')->nullable();
        $table->string('image_url');         // URL gambar untuk card
        $table->integer('order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('islands');
    }
};
