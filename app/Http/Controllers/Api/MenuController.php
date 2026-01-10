<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        // kategori untuk tabbar (tanpa all)
        $categories = DB::table('categories')
            ->where('is_active', true)
            ->where('slug', '!=', 'all')
            ->orderBy('sort_order')
            ->get(['id', 'name_short', 'slug', 'sort_order']);

        // produk aktif join kategori
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_active', true)
            ->where('categories.is_active', true)
            ->where('categories.slug', '!=', 'all')
            ->orderBy('categories.sort_order')
            ->orderBy('products.name')
            ->get([
                'products.id',
                'products.name',
                'products.slug',
                'products.series_title', // nama panjang buat subtitle per section
                'products.image_url',
                'products.price',
                'categories.slug as category_slug',
                'categories.name_short as category_name_short',
            ]);

        // group by category_slug (biar gampang render per tab)
        $grouped = [];
        foreach ($products as $p) {
            $grouped[$p->category_slug][] = $p;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'products_by_category' => $grouped,
            ],
        ]);
    }
}
