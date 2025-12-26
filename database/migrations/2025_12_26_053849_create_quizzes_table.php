<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();

            $table->string('scope')->default('global')->index(); // global|island|tribe
            $table->string('title')->default('Kuis Budaya Indonesia');

            $table->foreignId('island_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('tribe', 100)->nullable();

            $table->boolean('is_active')->default(true)->index();

            $table->index(['island_id', 'tribe']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
