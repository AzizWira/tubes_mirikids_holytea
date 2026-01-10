<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_nutritions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // estimasi per porsi (misal regular)
            $table->unsignedInteger('calories_kcal')->nullable(); // 110
            $table->decimal('sugar_g', 6, 2)->nullable();         // 18.00
            $table->decimal('protein_g', 6, 2)->nullable();       // 0.00
            $table->decimal('fat_g', 6, 2)->nullable();           // 0.00

            // catatan kecil (misal "Per porsi (Normal)")
            $table->string('note')->nullable();

            $table->timestamps();

            $table->unique('product_id'); // 1 produk = 1 data nutrisi (simple)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_nutritions');
    }
};
