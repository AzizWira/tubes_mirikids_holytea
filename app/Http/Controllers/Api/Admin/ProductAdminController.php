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
                'note' => null,
            ];
        }

        $decoded = json_decode($nutritionJson, true);
        if (!is_array($decoded)) {
            return [
                'calories_kcal' => null,
                'sugar_g' => null,
                'protein_g' => null,
                'fat_g' => null,
                'note' => null,
            ];
        }

        return [
            'calories_kcal' => $decoded['calories_kcal'] ?? null,
            'sugar_g' => $decoded['sugar_g'] ?? null,
            'protein_g' => $decoded['protein_g'] ?? null,
            'fat_g' => $decoded['fat_g'] ?? null,
            'note' => $decoded['note'] ?? null,
        ];
    }

    private function withComputedFields(object $row): array
    {
        $arr = (array) $row;
        $arr['nutrition'] = $this->decodeNutrition($arr['nutrition_json'] ?? null);
        return $arr;
    }

    private function fetchOptions(int $productId): array
    {
        $rows = DB::table('product_options')
            ->select(['id', 'type', 'label', 'sort_order'])
            ->where('product_id', $productId)
            ->where('is_active', 1)
            ->orderBy('type')
            ->orderBy('sort_order')
            ->get();

        $options = ['size' => [], 'ice' => [], 'sugar' => []];

        foreach ($rows as $r) {
            if (isset($options[$r->type])) {
                $options[$r->type][] = $r;
            }
        }

        return $options;
    }

    private function syncOptions(int $productId, array $options): void
    {
        $types = ['size', 'ice', 'sugar'];

        foreach ($types as $type) {
            $items = $options[$type] ?? [];
            $keepIds = [];

            foreach ($items as $i => $item) {
                $payload = [
                    'product_id' => $productId,
                    'type' => $type,
                    'label' => $item['label'],
                    'sort_order' => $item['sort_order'] ?? ($i + 1),
                    'is_active' => isset($item['is_active']) ? (int) $item['is_active'] : 1,
                    'updated_at' => now(),
                ];

                if (!empty($item['id'])) {
                    DB::table('product_options')
                        ->where('id', (int) $item['id'])
                        ->where('product_id', $productId)
                        ->update($payload);

                    $keepIds[] = (int) $item['id'];
                } else {
                    $payload['created_at'] = now();
                    $newId = DB::table('product_options')->insertGetId($payload);
                    $keepIds[] = $newId;
                }
            }

            // nonaktifkan yang tidak ada di payload
            DB::table('product_options')
                ->where('product_id', $productId)
                ->where('type', $type)
                ->when(count($keepIds) > 0, fn($q) => $q->whereNotIn('id', $keepIds))
                ->update(['is_active' => 0, 'updated_at' => now()]);
        }
    }

    private function validateRules(?string $id = null): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:180',
                $id
                    ? Rule::unique('products', 'slug')->ignore($id)
                    : Rule::unique('products', 'slug')
            ],
            'price' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],

            'gofood_url' => ['nullable', 'string', 'max:255'],
            'grabfood_url' => ['nullable', 'string', 'max:255'],
            'shopeefood_url' => ['nullable', 'string', 'max:255'],

            'nutrition' => ['nullable', 'array'],
            'nutrition.calories_kcal' => ['nullable', 'numeric'],
            'nutrition.sugar_g' => ['nullable', 'numeric'],
            'nutrition.protein_g' => ['nullable', 'numeric'],
            'nutrition.fat_g' => ['nullable', 'numeric'],
            'nutrition.note' => ['nullable', 'string', 'max:100'],

            'options' => ['nullable', 'array'],
            'options.size' => ['nullable', 'array'],
            'options.ice' => ['nullable', 'array'],
            'options.sugar' => ['nullable', 'array'],

            'options.size.*.id' => ['nullable', 'integer'],
            'options.size.*.label' => ['required', 'string', 'max:80'],
            'options.size.*.sort_order' => ['nullable', 'integer', 'min:1'],
            'options.size.*.is_active' => ['nullable', 'boolean'],

            'options.ice.*.id' => ['nullable', 'integer'],
            'options.ice.*.label' => ['required', 'string', 'max:80'],
            'options.ice.*.sort_order' => ['nullable', 'integer', 'min:1'],
            'options.ice.*.is_active' => ['nullable', 'boolean'],

            'options.sugar.*.id' => ['nullable', 'integer'],
            'options.sugar.*.label' => ['required', 'string', 'max:80'],
            'options.sugar.*.sort_order' => ['nullable', 'integer', 'min:1'],
            'options.sugar.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('products as p')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->select([
                'p.*',
                'c.name_short as category_name',
                'c.slug as category_slug',
                'c.series_title as series_title',
            ])
            ->orderByDesc('p.id');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('p.name', 'like', "%{$q}%")
                    ->orWhere('p.slug', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(10);

        $items->getCollection()->transform(function ($row) {
            $arr = (array) $row;
            $arr['nutrition'] = $this->decodeNutrition($arr['nutrition_json'] ?? null);
            return $arr;
        });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->validateRules(null));
        $now = now();

        $nutritionJson = isset($data['nutrition'])
            ? json_encode([
                'calories_kcal' => $data['nutrition']['calories_kcal'] ?? null,
                'sugar_g' => $data['nutrition']['sugar_g'] ?? null,
                'protein_g' => $data['nutrition']['protein_g'] ?? null,
                'fat_g' => $data['nutrition']['fat_g'] ?? null,
                'note' => $data['nutrition']['note'] ?? null,
            ])
            : null;

        $id = DB::table('products')->insertGetId([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'image_url' => $data['image_url'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'is_active' => $data['is_active'] ?? 1,

            'gofood_url' => $data['gofood_url'] ?? null,
            'grabfood_url' => $data['grabfood_url'] ?? null,
            'shopeefood_url' => $data['shopeefood_url'] ?? null,
            'nutrition_json' => $nutritionJson,

            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if (isset($data['options']) && is_array($data['options'])) {
            $this->syncOptions((int) $id, $data['options']);
        }

        $product = DB::table('products as p')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->select([
                'p.*',
                'c.name_short as category_name',
                'c.slug as category_slug',
                'c.series_title as series_title',
            ])
            ->where('p.id', $id)
            ->first();

        $arr = $product ? $this->withComputedFields($product) : [];
        $arr['options'] = $this->fetchOptions((int) $id);

        return response()->json(['success' => true, 'data' => $arr], 201);
    }

    public function show(string $id)
    {
        $product = DB::table('products as p')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->select([
                'p.*',
                'c.name_short as category_name',
                'c.slug as category_slug',
                'c.series_title as series_title',
            ])
            ->where('p.id', $id)
            ->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $arr = $this->withComputedFields($product);
        $arr['options'] = $this->fetchOptions((int) $id);

        return response()->json(['success' => true, 'data' => $arr]);
    }

    public function update(Request $request, string $id)
    {
        // ✅ ambil data lama untuk hapus file lama
        $old = DB::table('products')->select(['id', 'image_url'])->where('id', $id)->first();
        if (!$old) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $data = $request->validate($this->validateRules($id));

        $nutritionJson = isset($data['nutrition'])
            ? json_encode([
                'calories_kcal' => $data['nutrition']['calories_kcal'] ?? null,
                'sugar_g' => $data['nutrition']['sugar_g'] ?? null,
                'protein_g' => $data['nutrition']['protein_g'] ?? null,
                'fat_g' => $data['nutrition']['fat_g'] ?? null,
                'note' => $data['nutrition']['note'] ?? null,
            ])
            : null;

        DB::table('products')->where('id', $id)->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'image_url' => $data['image_url'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'is_active' => $data['is_active'] ?? 1,

            'gofood_url' => $data['gofood_url'] ?? null,
            'grabfood_url' => $data['grabfood_url'] ?? null,
            'shopeefood_url' => $data['shopeefood_url'] ?? null,
            'nutrition_json' => $nutritionJson,

            'updated_at' => now(),
        ]);

        // ✅ hapus gambar lama jika berubah (hanya upload kita)
        $oldImageUrl = $old->image_url;
        $newImageUrl = $data['image_url'] ?? null;

        if ($oldImageUrl && $oldImageUrl !== $newImageUrl) {
            if (str_starts_with($oldImageUrl, '/storage/products/')) {
                $relativePath = str_replace('/storage/', '', $oldImageUrl); // products/xxx.webp
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
        }

        if (isset($data['options']) && is_array($data['options'])) {
            $this->syncOptions((int) $id, $data['options']);
        }

        $updated = DB::table('products as p')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->select([
                'p.*',
                'c.name_short as category_name',
                'c.slug as category_slug',
                'c.series_title as series_title',
            ])
            ->where('p.id', $id)
            ->first();

        $arr = $updated ? $this->withComputedFields($updated) : [];
        $arr['options'] = $this->fetchOptions((int) $id);

        return response()->json(['success' => true, 'data' => $arr]);
    }

    public function destroy(string $id)
    {
        $product = DB::table('products')->select(['id', 'image_url'])->where('id', $id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        // ✅ hapus gambar (hanya upload kita)
        if ($product->image_url && str_starts_with($product->image_url, '/storage/products/')) {
            $relativePath = str_replace('/storage/', '', $product->image_url); // products/xxx.webp
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        DB::table('products')->where('id', $id)->delete();

        // opsional: ikut nonaktifkan options agar rapi
        DB::table('product_options')->where('product_id', (int) $id)->update([
            'is_active' => 0,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
