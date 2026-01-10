<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductNutritionSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->select('id', 'price')->get();

        foreach ($products as $p) {
            // dummy sederhana berdasarkan harga (biar beda-beda)
            $cal = $p->price <= 7000 ? 80 : ($p->price <= 10000 ? 140 : 190);
            $sugar = $p->price <= 7000 ? 12.00 : ($p->price <= 10000 ? 18.00 : 22.00);
            $protein = $p->price >= 10000 ? 2.50 : 0.50;
            $fat = $p->price >= 16000 ? 5.50 : 1.20;

            DB::table('product_nutritions')->updateOrInsert(
                ['product_id' => $p->id],
                [
                    'calories_kcal' => $cal,
                    'sugar_g' => $sugar,
                    'protein_g' => $protein,
                    'fat_g' => $fat,
                    'note' => 'Per porsi (estimasi)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
