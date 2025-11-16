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
            $table->foreignId('island_id')
                ->constrained('islands')
                ->onDelete('cascade');

            // suku: Aceh / Batak / Minangkabau (sementara string dulu)
            $table->string('tribe', 50);

            // tahun / waktu (pakai string supaya fleksibel: "Abad ke-14", "1900–1945")
            $table->string('year_label', 100);

            $table->string('title');
            $table->text('content');

            // link opsional
            $table->string('more_link')->nullable();

            // urutan di timeline (opsional)
            $table->unsignedInteger('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_histories');
    }
};
