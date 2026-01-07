<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_level_id')->constrained('game_levels')->cascadeOnDelete();

            $table->enum('type', ['mcq', 'fill']); // mcq = pilihan ganda, fill = isian singkat
            $table->text('question_text');         // wajib
            $table->string('image_path')->nullable(); // opsional

            // untuk MCQ
            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();
            $table->enum('correct_option', ['A','B','C','D'])->nullable();

            // untuk FILL (jawaban benar)
            $table->string('correct_text')->nullable(); // panjangnya jadi maxlength

            $table->unsignedInteger('order')->default(1);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['game_level_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_questions');
    }
};
