<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('best_sellers', function (Blueprint $table) {
            $table->id();

            // opsional: best seller bisa menunjuk product
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // "left" (maks 1), "right" (maks 6) -> validasi nanti di admin
            $table->enum('position', ['left', 'right']);

            $table->string('image_url'); // gambar best seller (sesuai desain)
            $table->string('title')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0); // untuk urut kanan 1-6

            $table->timestamps();

            $table->index(['position', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('best_sellers');
    }
};
