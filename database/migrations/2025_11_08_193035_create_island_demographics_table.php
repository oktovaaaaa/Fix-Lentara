<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('island_demographics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')
                  ->constrained('islands')
                  ->onDelete('cascade');

            // 'religion', 'ethnicity', 'language'
            $table->string('type');
            $table->string('label');
            $table->decimal('percentage', 5, 2); // 0.00 - 100.00
            $table->unsignedInteger('order')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('island_demographics');
    }
};
