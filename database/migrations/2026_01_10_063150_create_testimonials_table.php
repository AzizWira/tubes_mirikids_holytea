<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();

            // testimoni bisa spesifik ke produk, atau umum
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name')->nullable();     // nama pemberi testimoni
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5 (optional)
            $table->text('message');                // isi testimoni
            $table->string('avatar_url')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
