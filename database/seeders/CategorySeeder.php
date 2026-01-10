<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_short' => 'All', 'slug' => 'all', 'sort_order' => 0],
            ['name_short' => 'Tea', 'slug' => 'tea', 'sort_order' => 1],
            ['name_short' => 'Yakult', 'slug' => 'yakult', 'sort_order' => 2],
            ['name_short' => 'Milkshake', 'slug' => 'milkshake', 'sort_order' => 3],
            ['name_short' => 'Coffee', 'slug' => 'coffee', 'sort_order' => 4],
            ['name_short' => 'Cheese', 'slug' => 'cheese', 'sort_order' => 5],
        ];

        foreach ($categories as $c) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $c['slug']],
                [
                    'name_short' => $c['name_short'],
                    'is_active' => true,
                    'sort_order' => $c['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
