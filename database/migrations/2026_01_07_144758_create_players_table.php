<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();          // untuk login
            $table->string('pin_hash');                   // hash PIN 4 digit

            $table->string('nickname')->nullable();       // tampil di UI
            $table->unsignedTinyInteger('avatar_key')->default(1); // 1..5

            $table->unsignedInteger('xp_total')->default(0);
            $table->unsignedInteger('coins')->default(100);

            $table->unsignedTinyInteger('hearts')->default(5);
            $table->unsignedTinyInteger('hearts_max')->default(5);

            /**
             * Untuk regen hati realtime:
             * timestamp terakhir yang dipakai sebagai acuan refill
             */
            $table->timestamp('hearts_updated_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
