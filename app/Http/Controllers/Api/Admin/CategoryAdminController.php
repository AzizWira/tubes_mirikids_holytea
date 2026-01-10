<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryAdminController extends Controller
{
    public function index()
    {
        $items = DB::table('categories')
            ->where('slug', '!=', 'all')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();


        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_short' => ['required', 'string', 'max:80'],
            'slug' => ['required', 'string', 'max:80', Rule::unique('categories', 'slug')],
            'series_title' => ['nullable', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $now = now();

        $id = DB::table('categories')->insertGetId([
            'name_short' => $data['name_short'],
            'slug' => $data['slug'],
            'series_title' => $data['series_title'] ?? null,
            'sort_order' => $data['sort_order'] ?? 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $cat = DB::table('categories')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $cat,
        ], 201);
    }

    public function show(string $id)
    {
        $cat = DB::table('categories')->where('id', $id)->first();

        if (!$cat) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cat,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $cat = DB::table('categories')->where('id', $id)->first();
        if (!$cat) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'name_short' => ['required', 'string', 'max:80'],
            'slug' => ['required', 'string', 'max:80', Rule::unique('categories', 'slug')->ignore($id)],
            'series_title' => ['nullable', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::table('categories')->where('id', $id)->update([
            'name_short' => $data['name_short'],
            'slug' => $data['slug'],
            'series_title' => $data['series_title'] ?? null,
            'sort_order' => $data['sort_order'] ?? 1,
            'updated_at' => now(),
        ]);

        $updated = DB::table('categories')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $updated,
        ]);
    }

    public function destroy(string $id)
    {
        $exists = DB::table('categories')->where('id', $id)->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        DB::table('categories')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }
}
