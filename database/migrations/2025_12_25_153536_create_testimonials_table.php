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
Schema::create('testimonials', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedTinyInteger('rating'); // 1–5
    $table->text('message');
    $table->string('photo')->nullable();
    $table->string('session_id'); // ownership browser
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
