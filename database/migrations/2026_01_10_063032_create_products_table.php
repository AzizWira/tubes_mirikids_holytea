<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

            // nama panjang untuk subtitle section (contoh: AUTHENTIC TEA SERIES)
            $table->string('series_title')->nullable();

            // nama produk (untuk card + popup + detail)
            $table->string('name'); // contoh: "Original Tea"
            $table->string('slug')->unique(); // untuk URL detail, contoh: "original-tea"

            // deskripsi singkat untuk popup
            $table->text('short_description')->nullable();

            // harga
            $table->unsignedInteger('price'); // simpan angka saja (3000)

            // gambar utama (bisa url atau path storage)
            $table->string('image_url');

            // link order (popup)
            $table->text('gofood_url')->nullable();
            $table->text('grabfood_url')->nullable();
            $table->text('shopeefood_url')->nullable();


            // untuk "produk terbaru" di index (ambil yang newest + is_active)
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
