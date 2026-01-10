<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('island_id')
                ->constrained('islands')
                ->cascadeOnDelete();

            // Nama suku sesuai config tribes.php (contoh: Aceh, Batak, dll)
            $table->string('tribe_key', 120)->index();

            $table->string('name', 180);
            $table->string('location', 180)->nullable();
            $table->text('description')->nullable();

            // Dual image:
            // - image_url: link eksternal
            // - image_path: file upload (storage/public)
            $table->string('image_url', 1000)->nullable();
            $table->string('image_path', 600)->nullable();

            /**
             * ===============================
             * 360° VIEW (GOOGLE MAPS EMBED)
             * ===============================
             * Harus SAMA dengan field yang dipakai Controller + Model:
             * - pano_embed_url (iframe embed)
             * - pano_maps_url  (link maps biasa / shortlink)
             * - pano_label     (label/judul viewer)
             */
            $table->string('pano_embed_url', 1600)->nullable();
            $table->string('pano_maps_url', 1200)->nullable();
            $table->string('pano_label', 255)->nullable();

            // rating 0.0 - 5.0 (misal 4.5)
            $table->decimal('rating', 2, 1)->default(0);

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Kombinasi island + tribe untuk query cepat
            $table->index(['island_id', 'tribe_key']);

            // Index opsional (ini boleh, tapi sebenarnya mirip dengan index di atas)
            $table->index(['island_id', 'tribe_key', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
