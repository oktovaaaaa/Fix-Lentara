<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sumatera_histories', function (Blueprint $table) {
            $table->id();
            $table->enum('tribe', ['aceh', 'batak', 'minang']); // pilihan suku
            $table->string('period');      // contoh: "Abad ke-13" / "Tahun 1900"
            $table->string('title');       // judul card
            $table->text('body');          // isi singkat
            $table->string('more_link')->nullable(); // opsional
            $table->unsignedInteger('sort_order')->default(0); // urutan timeline
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sumatera_histories');
    }
};
