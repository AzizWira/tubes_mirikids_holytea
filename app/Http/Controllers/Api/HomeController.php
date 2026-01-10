<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $totalProducts = DB::table('products')
            ->where('is_active', 1)
            ->count();

        $totalCategories = DB::table('categories')
            ->where('is_active', 1)
            ->where('slug', '!=', 'all')
            ->count();

        $news = DB::table('news_banners')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get(['id', 'title', 'image_url', 'link_url', 'sort_order']);

        $bestLeft = DB::table('best_sellers')
            ->where('is_active', 1)
            ->where('position', 'left')
            ->orderBy('sort_order')
            ->limit(1)
            ->get(['id', 'title', 'image_url', 'product_id']);

        $bestRight = DB::table('best_sellers')
            ->where('is_active', 1)
            ->where('position', 'right')
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'title', 'image_url', 'product_id', 'sort_order']);

        $latestProducts = DB::table('products')
            ->where('is_active', 1)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get(['id', 'name', 'slug', 'price', 'image_url', 'category_id']);

        $settings = DB::table('site_settings')->pluck('value', 'key');

        $mapsUrl = $settings['maps_url'] ?? null;
        $mapsEmbedUrl = $this->toMapsEmbedUrl($mapsUrl);

        return response()->json([
            'success' => true,
            'data' => [
                'counts' => [
                    'varian_menu' => $totalProducts,
                    'varian_rasa' => $totalCategories,
                ],
                'news' => $news,
                'best_seller' => [
                    'left' => $bestLeft,
                    'right' => $bestRight,
                ],
                'latest_products' => $latestProducts,
                'site' => [
                    'maps_url' => $mapsUrl,
                    'maps_embed_url' => $mapsEmbedUrl,
                    'address' => $settings['address'] ?? null,
                    'open_days' => $settings['open_days'] ?? null,
                    'open_hours' => $settings['open_hours'] ?? null,
                    'friday_hours' => $settings['friday_hours'] ?? null,
                    'phone' => $settings['phone'] ?? null,
                    'email' => $settings['email'] ?? null,
                    'instagram_url' => $settings['instagram_url'] ?? null,
                ],
            ],
        ]);
    }

    private function toMapsEmbedUrl(?string $url): ?string
    {
        if (!$url)
            return null;

        // kalau sudah embed, pakai langsung
        if (Str::contains($url, 'google.com/maps/embed') || Str::contains($url, 'output=embed')) {
            return $url;
        }

        return 'https://www.google.com/maps?q=' . urlencode($url) . '&output=embed';
    }
}
