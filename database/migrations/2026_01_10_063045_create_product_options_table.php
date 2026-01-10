<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // contoh: "size", "ice", "sugar"
            $table->enum('type', ['size', 'ice', 'sugar']);

            // label tampilan, contoh:
            // size: "Regular (350ml)"
            // ice: "Ice / Less Ice"
            // sugar: "Normal / Less Sugar"
            $table->string('label');

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['product_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
