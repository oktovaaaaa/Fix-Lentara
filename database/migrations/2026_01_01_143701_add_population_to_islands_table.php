<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('islands', function (Blueprint $table) {
            if (!Schema::hasColumn('islands', 'population')) {
                $table->unsignedBigInteger('population')->nullable()->after('short_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('islands', function (Blueprint $table) {
            if (Schema::hasColumn('islands', 'population')) {
                $table->dropColumn('population');
            }
        });
    }
};
