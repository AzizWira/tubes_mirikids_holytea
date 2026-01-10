<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private function defaultOptions(): array
    {
        return [
            'size' => [
                ['id' => 1, 'type' => 'size', 'label' => 'Regular', 'sort_order' => 1],
                ['id' => 2, 'type' => 'size', 'label' => 'Large', 'sort_order' => 2],
            ],
            'ice' => [
                ['id' => 3, 'type' => 'ice', 'label' => 'Ice', 'sort_order' => 1],
                ['id' => 4, 'type' => 'ice', 'label' => 'Less Ice', 'sort_order' => 2],
                ['id' => 5, 'type' => 'ice', 'label' => 'No Ice', 'sort_order' => 3],
            ],
            'sugar' => [
                ['id' => 6, 'type' => 'sugar', 'label' => 'Normal Sugar', 'sort_order' => 1],
                ['id' => 7, 'type' => 'sugar', 'label' => 'Less Sugar', 'sort_order' => 2],
                ['id' => 8, 'type' => 'sugar', 'label' => 'No Sugar', 'sort_order' => 3],
            ],
        ];
    }

    private function decodeNutrition(?string $nutritionJson): object
    {
        if (!$nutritionJson) {
            return (object) [
                'calories_kcal' => null,
                'sugar_g' => null,
                'protein_g' => null,
                'fat_g' => null,
                'note' => null,
            ];
        }

        $decoded = json_decode($nutritionJson, true);
        if (!is_array($decoded)) {
            return (object) [
                'calories_kcal' => null,
                'sugar_g' => null,
                'protein_g' => null,
                'fat_g' => null,
                'note' => null,
            ];
        }

        return (object) [
            'calories_kcal' => $decoded['calories_kcal'] ?? null,
            'sugar_g' => $decoded['sugar_g'] ?? null,
            'protein_g' => $decoded['protein_g'] ?? null,
            'fat_g' => $decoded['fat_g'] ?? null,
            'note' => $decoded['note'] ?? null,
        ];
    }

    public function show(string $slug)
    {
        try {
            // 1) Ambil product + category
            //    - series_title dari category
            //    - nutrition_json dari products (hasil input admin)
            $product = DB::table('products as p')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->select([
                    'p.id',
                    'p.name',
                    'p.slug',
                    DB::raw('c.series_title as series_title'),
                    'p.short_description',
                    'p.price',
                    'p.image_url',
                    'p.gofood_url',
                    'p.grabfood_url',
                    'p.shopeefood_url',
                    'p.category_id',
                    'p.nutrition_json',
                    DB::raw('c.slug as category_slug'),
                    DB::raw('c.name_short as category_name'),
                ])
                ->where('p.slug', $slug)
                ->where('p.is_active', 1)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan',
                ], 404);
            }

            // 2) Options
            // Prioritas:
            // A) jika product_options ada -> pakai itu
            // B) kalau kosong / tabel tidak ada -> pakai default radio options
            $options = [
                'size' => [],
                'ice' => [],
                'sugar' => [],
            ];

            try {
                $optionsRows = DB::table('product_options')
                    ->select(['id', 'type', 'label', 'sort_order'])
                    ->where('product_id', $product->id)
                    ->where('is_active', 1)
                    ->orderBy('type')
                    ->orderBy('sort_order')
                    ->get();

                foreach ($optionsRows as $row) {
                    if (isset($options[$row->type])) {
                        $options[$row->type][] = $row;
                    }
                }

                // fallback kalau data kosong
                if (
                    count($options['size']) === 0 &&
                    count($options['ice']) === 0 &&
                    count($options['sugar']) === 0
                ) {
                    $options = $this->defaultOptions();
                }
            } catch (\Throwable $e) {
                // fallback jika tabel belum ada / error
                $options = $this->defaultOptions();
            }

            // 3) Nutrition
            // Prioritas:
            // A) products.nutrition_json (yang diinput admin)
            // B) fallback ke product_nutritions (kalau masih dipakai)
            $nutrition = $this->decodeNutrition($product->nutrition_json ?? null);

            if (
                $nutrition->calories_kcal === null &&
                $nutrition->sugar_g === null &&
                $nutrition->protein_g === null &&
                $nutrition->fat_g === null &&
                $nutrition->note === null
            ) {
                // fallback ke table lama jika ada
                try {
                    $legacy = DB::table('product_nutritions')
                        ->select(['calories_kcal', 'sugar_g', 'protein_g', 'fat_g', 'note'])
                        ->where('product_id', $product->id)
                        ->first();

                    if ($legacy) {
                        $nutrition = $legacy;
                    }
                } catch (\Throwable $e) {
                    // ignore, tetap pakai default null
                }
            }

            // 4) Testimonials
            $testimonials = [];
            try {
                $testimonials = DB::table('testimonials')
                    ->select(['id', 'name', 'rating', 'message', 'created_at'])
                    ->where('product_id', $product->id)
                    ->where('is_active', 1)
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get();
            } catch (\Throwable $e) {
                $testimonials = collect([]);
            }

            // 5) Rating summary
            $avgRating = null;
            $totalReviews = 0;

            try {
                $ratingAgg = DB::table('testimonials')
                    ->where('product_id', $product->id)
                    ->where('is_active', 1)
                    ->selectRaw('COUNT(*) as total_reviews, AVG(rating) as avg_rating')
                    ->first();

                $avgRating = $ratingAgg && $ratingAgg->avg_rating !== null
                    ? round((float) $ratingAgg->avg_rating, 1)
                    : null;

                $totalReviews = $ratingAgg ? (int) $ratingAgg->total_reviews : 0;
            } catch (\Throwable $e) {
                // ignore
            }

            // Bersihkan field internal agar FE tidak bingung
            unset($product->nutrition_json);

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'options' => $options,
                    'nutrition' => $nutrition,
                    'testimonials' => $testimonials,
                    'rating' => [
                        'avg' => $avgRating,
                        'count' => $totalReviews,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
