<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductAdminController extends Controller
{
    private function decodeNutrition(?string $nutritionJson): array
    {
        if (!$nutritionJson) {
            return [
                'calories_kcal' => null,
                'sugar_g' => null,
                'protein_g' => null,
                'fat_g' => null,
                'sodium_mg' => null,
            ];
        }

        $arr = json_decode($nutritionJson, true);
        if (!is_array($arr)) {
            return [
                'calories_kcal' => null,
                'sugar_g' => null,
                'protein_g' => null,
                'fat_g' => null,
                'sodium_mg' => null,
            ];
        }

        return array_merge([
            'calories_kcal' => null,
            'sugar_g' => null,
            'protein_g' => null,
            'fat_g' => null,
            'sodium_mg' => null,
        ], $arr);
    }

    private function mapProductRow($row): array
    {
        $images = [];
        if (!empty($row->images_json)) {
            $decoded = json_decode($row->images_json, true);
            if (is_array($decoded)) {
                $images = $decoded;
            }
        }

        return [
            'id' => $row->id,
            'category_id' => $row->category_id,
            'name' => $row->name,
            'slug' => $row->slug,
            'description' => $row->description,
            'price' => $row->price,
            'is_active' => (int) $row->is_active,
            'is_best_seller' => (int) $row->is_best_seller,
            'sort_order' => $row->sort_order,
            'images' => $images,
            'nutrition' => $this->decodeNutrition($row->nutrition_json ?? null),
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }

    public function index()
    {
        $items = DB::table('products as p')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->select(
                'p.*',
                'c.name as category_name'
            )
            ->orderByDesc('p.id')
            ->get();

        $data = $items->map(function ($row) {
            $mapped = $this->mapProductRow($row);
            $mapped['category_name'] = $row->category_name;
            return $mapped;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:220', 'unique:products,slug'],
            'description' => ['nullable', 'string'],

            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_best_seller' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            // images = array of strings (storage path or url)
            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:500'],

            // nutrition
            'nutrition' => ['nullable', 'array'],
            'nutrition.calories_kcal' => ['nullable', 'numeric', 'min:0'],
            'nutrition.sugar_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.protein_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.fat_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.sodium_mg' => ['nullable', 'numeric', 'min:0'],
        ]);

        $nutritionJson = null;
        if (!empty($data['nutrition']) && is_array($data['nutrition'])) {
            $nutritionJson = json_encode($data['nutrition']);
        }

        $imagesJson = null;
        if (!empty($data['images']) && is_array($data['images'])) {
            $imagesJson = json_encode(array_values($data['images']));
        }

        $id = DB::table('products')->insertGetId([
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'is_active' => $data['is_active'] ?? 1,
            'is_best_seller' => $data['is_best_seller'] ?? 0,
            'sort_order' => $data['sort_order'] ?? 0,
            'images_json' => $imagesJson,
            'nutrition_json' => $nutritionJson,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $row = DB::table('products')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $this->mapProductRow($row),
        ], 201);
    }

    public function show(string $id)
    {
        $row = DB::table('products')->where('id', $id)->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->mapProductRow($row),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $row = DB::table('products')->where('id', $id)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $data = $request->validate([
            'category_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:200'],
            'slug' => [
                'required',
                'string',
                'max:220',
                Rule::unique('products', 'slug')->ignore($id),
            ],
            'description' => ['nullable', 'string'],

            'price' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_best_seller' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:500'],

            'nutrition' => ['nullable', 'array'],
            'nutrition.calories_kcal' => ['nullable', 'numeric', 'min:0'],
            'nutrition.sugar_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.protein_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.fat_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.sodium_mg' => ['nullable', 'numeric', 'min:0'],
        ]);

        $nutritionJson = null;
        if (!empty($data['nutrition']) && is_array($data['nutrition'])) {
            $nutritionJson = json_encode($data['nutrition']);
        }

        $imagesJson = null;
        if (!empty($data['images']) && is_array($data['images'])) {
            $imagesJson = json_encode(array_values($data['images']));
        }

        DB::table('products')->where('id', $id)->update([
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'is_active' => $data['is_active'] ?? 1,
            'is_best_seller' => $data['is_best_seller'] ?? 0,
            'sort_order' => $data['sort_order'] ?? 0,
            'images_json' => $imagesJson,
            'nutrition_json' => $nutritionJson,
            'updated_at' => now(),
        ]);

        $updated = DB::table('products')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'data' => $this->mapProductRow($updated),
        ]);
    }

    public function destroy(string $id)
    {
        $row = DB::table('products')->where('id', $id)->first();
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // (opsional) hapus file images dari storage jika kamu simpan path storage
        if (!empty($row->images_json)) {
            $images = json_decode($row->images_json, true);
            if (is_array($images)) {
                foreach ($images as $img) {
                    // kalau formatnya /storage/xxx -> convert ke disk path
                    $path = $img;
                    if (is_string($img) && str_starts_with($img, '/storage/')) {
                        $path = str_replace('/storage/', '', $img);
                    }
                    if (is_string($path) && $path !== '') {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }

        DB::table('products')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted',
        ]);
    }
}
