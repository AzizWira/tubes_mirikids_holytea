<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BestSellerSeeder extends Seeder
{
    public function run(): void
    {
        // kiri besar (1)
        DB::table('best_sellers')->updateOrInsert(
            ['position' => 'left', 'sort_order' => 1],
            [
                'product_id' => null,
                'image_url' => '/assets/best-seller/best-kiri.png',
                'title' => 'Best Seller',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // kanan (6)
        $right = [
            ['/assets/best-seller/best-kanan-1.jpg', 1],
            ['/assets/best-seller/best-kanan-2.jpg', 2],
            ['/assets/best-seller/best-kanan-3.jpg', 3],
            ['/assets/best-seller/best-kanan-4.jpg', 4],
            ['/assets/best-seller/best-kanan-5.jpg', 5],
            ['/assets/best-seller/best-kanan-6.jpg', 6],
        ];

        foreach ($right as [$img, $order]) {
            DB::table('best_sellers')->updateOrInsert(
                ['position' => 'right', 'sort_order' => $order],
                [
                    'product_id' => null,
                    'image_url' => $img,
                    'title' => 'Best Seller ' . $order,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
