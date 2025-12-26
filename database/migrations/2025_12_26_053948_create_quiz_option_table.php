<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quiz_question_id')
                ->constrained('quiz_questions')
                ->cascadeOnDelete();

            $table->string('content_type')->default('text'); // text|image
            $table->text('content_text')->nullable();
            $table->string('content_image')->nullable();

            $table->boolean('is_correct')->default(false)->index();
            $table->unsignedInteger('order')->default(0)->index();

            $table->timestamps();
            $table->index(['quiz_question_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
    }
};
