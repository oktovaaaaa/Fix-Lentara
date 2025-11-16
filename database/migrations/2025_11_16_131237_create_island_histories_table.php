<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('island_histories', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel islands (Sumatera, Jawa, Kalimantan, Sulawesi, Bali, Papua)
            $table->foreignId('island_id')
                ->constrained('islands')
                ->onDelete('cascade');

            // Nama suku untuk semua pulau (Aceh, Batak, Minangkabau, Suku Jawa, Dayak, Bali, dsb.)
            $table->string('tribe', 100);

            // Tahun / waktu (fleksibel: "Abad ke-14", "1900–1945", "Sejak zaman kerajaan", dll.)
            $table->string('year_label', 100);

            // Judul sejarah
            $table->string('title');

            // Isi sejarah
            $table->text('content');

            // Link sumber / baca selengkapnya (opsional)
            $table->string('more_link')->nullable();

            // Urutan di timeline (0, 1, 2, ...)
            $table->unsignedInteger('order')->default(0);

            $table->timestamps();

            // Index supaya pencarian per pulau & suku lebih cepat
            $table->index(['island_id', 'tribe', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_histories');
    }
};
