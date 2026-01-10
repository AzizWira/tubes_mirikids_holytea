<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestimonialAdminController extends Controller
{
    /**
     * Untuk dropdown select produk (Admin2).
     */
    public function productOptions()
    {
        $items = DB::table('products')
            ->select(['id', 'name', 'slug'])
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * List testimoni (dengan nama produk).
     * Query params:
     * - q (search name/message/product)
     * - product_id (filter)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $productId = (int) $request->query('product_id', 0);

        $query = DB::table('testimonials as t')
            ->leftJoin('products as p', 'p.id', '=', 't.product_id')
            ->select([
                't.id',
                't.product_id',
                'p.name as product_name',
                'p.slug as product_slug',
                't.name',
                't.rating',
                't.message',
                't.avatar_url',
                't.is_active',
                't.created_at',
                't.updated_at',
            ])
            ->orderByDesc('t.id');

        if ($productId > 0) {
            $query->where('t.product_id', $productId);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('t.name', 'like', "%{$q}%")
                    ->orWhere('t.message', 'like', "%{$q}%")
                    ->orWhere('p.name', 'like', "%{$q}%")
                    ->orWhere('p.slug', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Tambah testimoni (product wajib).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'name' => ['nullable', 'string', 'max:80'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'message' => ['required', 'string'],
            'avatar_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $id = DB::table('testimonials')->insertGetId([
            'product_id' => (int) $data['product_id'],
            'name' => $data['name'] ?? null,
            'rating' => isset($data['rating']) ? (int) $data['rating'] : null,
            'message' => $data['message'],
            'avatar_url' => $data['avatar_url'] ?? null,
            'is_active' => array_key_exists('is_active', $data) ? (int) $data['is_active'] : 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->show((string) $id)->setStatusCode(201);
    }

    /**
     * Detail.
     */
    public function show(string $id)
    {
        $row = DB::table('testimonials as t')
            ->leftJoin('products as p', 'p.id', '=', 't.product_id')
            ->select([
                't.id',
                't.product_id',
                'p.name as product_name',
                'p.slug as product_slug',
                't.name',
                't.rating',
                't.message',
                't.avatar_url',
                't.is_active',
                't.created_at',
                't.updated_at',
            ])
            ->where('t.id', (int) $id)
            ->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Testimoni tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $row]);
    }

    /**
     * Update.
     */
    public function update(Request $request, string $id)
    {
        $exists = DB::table('testimonials')->where('id', (int) $id)->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Testimoni tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'name' => ['nullable', 'string', 'max:80'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'message' => ['required', 'string'],
            'avatar_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('testimonials')->where('id', (int) $id)->update([
            'product_id' => (int) $data['product_id'],
            'name' => $data['name'] ?? null,
            'rating' => isset($data['rating']) ? (int) $data['rating'] : null,
            'message' => $data['message'],
            'avatar_url' => $data['avatar_url'] ?? null,
            'is_active' => array_key_exists('is_active', $data) ? (int) $data['is_active'] : 1,
            'updated_at' => now(),
        ]);

        return $this->show($id);
    }

    /**
     * Hapus.
     */
    public function destroy(string $id)
    {
        $exists = DB::table('testimonials')->where('id', (int) $id)->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Testimoni tidak ditemukan'], 404);
        }

        DB::table('testimonials')->where('id', (int) $id)->delete();

        return response()->json(['success' => true]);
    }
}
