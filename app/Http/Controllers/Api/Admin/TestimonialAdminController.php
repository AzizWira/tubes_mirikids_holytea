<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestimonialAdminController extends Controller
{
    public function productOptions()
    {
        $items = DB::table('products')
            ->select('id', 'name', 'slug')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function index()
    {
        $items = DB::table('testimonials as t')
            ->leftJoin('products as p', 'p.id', '=', 't.product_id')
            ->select(
                't.*',
                'p.name as product_name',
                'p.slug as product_slug'
            )
            ->orderByDesc('t.id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string'],
            'product_id' => ['nullable', 'integer'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $id = DB::table('testimonials')->insertGetId([
            'name' => $data['name'],
            'message' => $data['message'],
            'product_id' => $data['product_id'] ?? null,
            'rating' => $data['rating'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $row = DB::table('testimonials')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $row,
        ], 201);
    }

    public function show(string $id)
    {
        $row = DB::table('testimonials')->where('id', $id)->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Testimonial not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $row,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $row = DB::table('testimonials')->where('id', $id)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Testimonial not found'], 404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string'],
            'product_id' => ['nullable', 'integer'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('testimonials')->where('id', $id)->update([
            'name' => $data['name'],
            'message' => $data['message'],
            'product_id' => $data['product_id'] ?? null,
            'rating' => $data['rating'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'updated_at' => now(),
        ]);

        $updated = DB::table('testimonials')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $updated,
        ]);
    }

    public function destroy(string $id)
    {
        $row = DB::table('testimonials')->where('id', $id)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Testimonial not found'], 404);
        }

        DB::table('testimonials')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted',
        ]);
    }
}
