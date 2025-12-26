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
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();

            // RELASI ke pertanyaan quiz
            $table->foreignId('quiz_question_id')
                ->constrained('quiz_questions')
                ->cascadeOnDelete();

            // Jenis konten jawaban
            $table->enum('content_type', ['text', 'image'])->default('text');

            // Isi jawaban
            $table->text('content_text')->nullable();
            $table->string('content_image')->nullable();

            // Penanda jawaban benar
            $table->boolean('is_correct')->default(false);

            // Urutan opsi
            $table->unsignedInteger('order')->default(0);

            $table->timestamps();

            // Index biar cepat
            $table->index(['quiz_question_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
    }
};
