<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        // total produk aktif
        $totalProducts = DB::table('products')
            ->where('is_active', 1)
            ->count();

        // banner
        $banners = DB::table('news_banners')
            ->select('id', 'title', 'image_url', 'link_url', 'sort_order')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // best sellers (left: max 1, right: max 6)
        $bestSellers = DB::table('best_sellers as bs')
            ->leftJoin('products as p', 'p.id', '=', 'bs.product_id')
            ->select(
                'bs.id',
                'bs.position',
                'bs.image_url',
                'bs.title',
                'bs.sort_order',
                'p.id as product_id',
                'p.name as product_name',
                'p.slug as product_slug',
                'p.price as product_price',
                'p.image_url as product_image_url'
            )
            ->where('bs.is_active', 1)
            ->orderByRaw("CASE WHEN bs.position = 'left' THEN 0 ELSE 1 END")
            ->orderBy('bs.sort_order')
            ->orderBy('bs.id')
            ->get();

        $bestSellerLeft = $bestSellers->firstWhere('position', 'left');
        $bestSellerRight = $bestSellers->where('position', 'right')->values();

        // settings (key/value)
        $settingsRows = DB::table('site_settings')->select('key', 'value')->get();
        $settings = [];
        foreach ($settingsRows as $row) {
            $settings[$row->key] = $row->value;
        }

        // maps_url -> embed url (kalau user tempel alamat/link biasa)
        if (!empty($settings['maps_url'])) {
            $settings['maps_embed_url'] = $this->toMapsEmbedUrl($settings['maps_url']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_products' => $totalProducts,
                ],
                'banners' => $banners,
                'best_seller' => [
                    'left' => $bestSellerLeft,
                    'right' => $bestSellerRight,
                ],
                'settings' => $settings,
            ],
        ]);
    }

    private function toMapsEmbedUrl(?string $url): ?string
    {
        if (!$url) return null;

        // kalau sudah embed, pakai langsung
        if (Str::contains($url, 'google.com/maps/embed') || Str::contains($url, 'output=embed')) {
            return $url;
        }

        // kalau bukan embed, ubah jadi query embed
        return 'https://www.google.com/maps?q=' . urlencode($url) . '&output=embed';
    }
}
