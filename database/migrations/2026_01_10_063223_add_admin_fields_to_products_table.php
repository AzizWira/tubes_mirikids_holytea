<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Tambah hanya kalau belum ada
            if (!Schema::hasColumn('products', 'gofood_url')) {
                $table->string('gofood_url')->nullable()->after('image_url');
            }

            if (!Schema::hasColumn('products', 'grabfood_url')) {
                $table->string('grabfood_url')->nullable()->after('gofood_url');
            }

            if (!Schema::hasColumn('products', 'shopeefood_url')) {
                $table->string('shopeefood_url')->nullable()->after('grabfood_url');
            }

            // Kalau kamu butuh options dan nutrition disimpan JSON
            if (!Schema::hasColumn('products', 'options_json')) {
                $table->json('options_json')->nullable()->after('shopeefood_url');
            }

            if (!Schema::hasColumn('products', 'nutrition_json')) {
                $table->json('nutrition_json')->nullable()->after('options_json');
            }

            // Jangan tambahkan series_title kalau sudah ada
            // if (!Schema::hasColumn('products', 'series_title')) {
            //     $table->string('series_title')->nullable()->after('slug');
            // }

            // Jangan tambahkan short_description kalau sudah ada
            // if (!Schema::hasColumn('products', 'short_description')) {
            //     $table->text('short_description')->nullable()->after('series_title');
            // }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'nutrition_json')) {
                $table->dropColumn('nutrition_json');
            }
            if (Schema::hasColumn('products', 'options_json')) {
                $table->dropColumn('options_json');
            }
            if (Schema::hasColumn('products', 'shopeefood_url')) {
                $table->dropColumn('shopeefood_url');
            }
            if (Schema::hasColumn('products', 'grabfood_url')) {
                $table->dropColumn('grabfood_url');
            }
            if (Schema::hasColumn('products', 'gofood_url')) {
                $table->dropColumn('gofood_url');
            }
        });
    }
};
