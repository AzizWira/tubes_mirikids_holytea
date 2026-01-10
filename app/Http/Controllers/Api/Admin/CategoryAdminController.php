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
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:120', 'unique:categories,slug'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $id = DB::table('categories')->insertGetId([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $row = DB::table('categories')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $row,
        ], 201);
    }

    public function show(string $id)
    {
        $row = DB::table('categories')->where('id', $id)->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $row,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $row = DB::table('categories')->where('id', $id)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                'string',
                'max:120',
                Rule::unique('categories', 'slug')->ignore($id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('categories')->where('id', $id)->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
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
        $row = DB::table('categories')->where('id', $id)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        DB::table('categories')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted',
        ]);
    }
}
