<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        // kategori untuk tabbar (tanpa "all")
        $categories = DB::table('categories')
            ->select('id', 'name_short', 'slug', 'sort_order')
            ->where('is_active', 1)
            ->where('slug', '!=', 'all')
            ->orderBy('sort_order')
            ->orderBy('name_short')
            ->get();

        // daftar produk aktif
        $products = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->select(
                'p.id',
                'p.category_id',
                'p.series_title',
                'p.name',
                'p.slug',
                'p.short_description',
                'p.price',
                'p.image_url',
                'c.slug as category_slug'
            )
            ->where('p.is_active', 1)
            ->where('c.is_active', 1)
            ->orderBy('c.sort_order')
            ->orderBy('p.id')
            ->get();

        // group by kategori slug
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
