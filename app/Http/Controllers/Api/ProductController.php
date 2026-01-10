<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private function defaultOptions(): array
    {
        // fallback jika table product_options kosong (biar FE tetap jalan)
        return [
            'size' => [
                ['id' => 1, 'type' => 'size', 'label' => 'Regular', 'sort_order' => 1],
                ['id' => 2, 'type' => 'size', 'label' => 'Large', 'sort_order' => 2],
            ],
            'ice' => [
                ['id' => 1, 'type' => 'ice', 'label' => 'Normal Ice', 'sort_order' => 1],
                ['id' => 2, 'type' => 'ice', 'label' => 'Less Ice', 'sort_order' => 2],
                ['id' => 3, 'type' => 'ice', 'label' => 'No Ice', 'sort_order' => 3],
            ],
            'sugar' => [
                ['id' => 1, 'type' => 'sugar', 'label' => 'Normal Sugar', 'sort_order' => 1],
                ['id' => 2, 'type' => 'sugar', 'label' => 'Less Sugar', 'sort_order' => 2],
                ['id' => 3, 'type' => 'sugar', 'label' => 'No Sugar', 'sort_order' => 3],
            ],
        ];
    }

    public function show(string $slug)
    {
        try {
            $product = DB::table('products as p')
                ->join('categories as c', 'c.id', '=', 'p.category_id')
                ->select(
                    'p.id',
                    'p.category_id',
                    'c.name_short as category_name',
                    'c.slug as category_slug',

                    'p.series_title',
                    'p.name',
                    'p.slug',
                    'p.short_description',
                    'p.price',
                    'p.image_url',
                    'p.gofood_url',
                    'p.grabfood_url',
                    'p.shopeefood_url'
                )
                ->where('p.slug', $slug)
                ->where('p.is_active', 1)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // nutrition (1 produk = 1 row)
            $nutrition = DB::table('product_nutritions')
                ->select('calories_kcal', 'sugar_g', 'protein_g', 'fat_g', 'note')
                ->where('product_id', $product->id)
                ->first();

            // options
            $optionsRows = DB::table('product_options')
                ->select('id', 'type', 'label', 'sort_order')
                ->where('product_id', $product->id)
                ->where('is_active', 1)
                ->orderBy('type')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $options = [
                'size' => [],
                'ice' => [],
                'sugar' => [],
            ];

            foreach ($optionsRows as $row) {
                if (isset($options[$row->type])) {
                    $options[$row->type][] = $row;
                }
            }

            $hasAnyOptions = count($options['size']) + count($options['ice']) + count($options['sugar']) > 0;
            if (!$hasAnyOptions) {
                $options = $this->defaultOptions();
            }

            // testimonials + rating aggregate (optional)
            $testimonials = DB::table('testimonials')
                ->select('id', 'name', 'rating', 'message', 'avatar_url', 'created_at')
                ->where('is_active', 1)
                ->where('product_id', $product->id)
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            $ratingAgg = DB::table('testimonials')
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
                ->where('is_active', 1)
                ->where('product_id', $product->id)
                ->whereNotNull('rating')
                ->first();

            $avgRating = $ratingAgg && $ratingAgg->avg_rating !== null
                ? round((float) $ratingAgg->avg_rating, 1)
                : null;

            $totalReviews = $ratingAgg ? (int) $ratingAgg->total_reviews : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'nutrition' => $nutrition,
                    'options' => $options,
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
