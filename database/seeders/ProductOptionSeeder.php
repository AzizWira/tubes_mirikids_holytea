<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductOptionSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->select('id')->get();

        foreach ($products as $product) {
            $base = [
                // Size
                ['type' => 'size', 'label' => 'Regular', 'sort_order' => 1],
                ['type' => 'size', 'label' => 'Large', 'sort_order' => 2],

                // Ice
                ['type' => 'ice', 'label' => 'Ice', 'sort_order' => 1],
                ['type' => 'ice', 'label' => 'Less Ice', 'sort_order' => 2],
                ['type' => 'ice', 'label' => 'No Ice', 'sort_order' => 3],

                // Sugar
                ['type' => 'sugar', 'label' => 'Normal Sugar', 'sort_order' => 1],
                ['type' => 'sugar', 'label' => 'Less Sugar', 'sort_order' => 2],
                ['type' => 'sugar', 'label' => 'No Sugar', 'sort_order' => 3],
            ];

            foreach ($base as $opt) {
                DB::table('product_options')->updateOrInsert(
                    [
                        'product_id' => $product->id,
                        'type' => $opt['type'],
                        'label' => $opt['label'],
                    ],
                    [
                        'is_active' => true,
                        'sort_order' => $opt['sort_order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
