<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsBannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['/assets/sponsor/poster-rasabaru-lemonade.jpg', 'Varian Baru Lemonade', null, 1],
            ['/assets/sponsor/poster-order.jpg', 'Order Sekarang', null, 2],
            ['/assets/sponsor/poster-rasabaru-blackforest.jpg', 'Varian Baru Black Forest', null, 3],
            ['/assets/sponsor/outlet baru.jpg', 'Outlet Baru', null, 4],
        ];

        foreach ($banners as $b) {
            DB::table('news_banners')->updateOrInsert(
                ['image_url' => $b[0]],
                [
                    'title' => $b[1],
                    'link_url' => $b[2],
                    'is_active' => true,
                    'sort_order' => $b[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
