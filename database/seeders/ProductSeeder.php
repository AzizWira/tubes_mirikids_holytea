<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $gofood = 'https://gofood.link/a/FPu5BLq';
        $grab = 'https://grab.onelink.me/2695613898?pid=inappsharing&c=6-C3EYAEWXWAUHA6&is_retargeting=true&af_dp=grab%3A%2F%2Fopen%3FscreenType%3DGRABFOOD%26sourceID%3DA4pcqCZkS4%26merchantIDs%3D6-C3EYAEWXWAUHA6&af_force_deeplink=true&af_web_dp=https%3A%2F%2Fwww.grab.com%2Fdownload';
        $shopee = 'https://shopee.co.id/universal-link/now-food/shop/20939777?deep_and_deferred=1&shareChannel=copy_link';

        $catId = fn(string $slug) => DB::table('categories')->where('slug', $slug)->value('id');

        $products = [
            // ================= TEA =================
            [
                'category_slug' => 'tea',
                'series_title' => 'AUTHENTIC TEA SERIES',
                'name' => 'Original Tea',
                'short_description' => 'Kombinasi dari beberapa jenis teh pilihan yang berbeda, diracik dengan bahan pelengkap seperti gula cair dan es batu kristal.',
                'price' => 3000,
                'image_url' => '/assets/tea-series/original-tea.svg',
            ],
            [
                'category_slug' => 'tea',
                'series_title' => 'AUTHENTIC TEA SERIES',
                'name' => 'Lychee Tea',
                'short_description' => 'Original tea dengan tambahan syrup lychee dan bahan pelengkap lain yang bikin seger di cuaca terik.',
                'price' => 3000,
                'image_url' => '/assets/tea-series/lychee-tea.svg',
            ],
            [
                'category_slug' => 'tea',
                'series_title' => 'AUTHENTIC TEA SERIES',
                'name' => 'Markisa Tea',
                'short_description' => 'Original tea dengan syrup markisa dan bahan pelengkap lain yang bikin seger.',
                'price' => 5000,
                'image_url' => '/assets/tea-series/markisa-tea.svg',
            ],
            [
                'category_slug' => 'tea',
                'series_title' => 'AUTHENTIC TEA SERIES',
                'name' => 'Strawberry Tea',
                'short_description' => 'Original tea dengan syrup strawberry dan bahan pelengkap lain yang bikin seger.',
                'price' => 5000,
                'image_url' => '/assets/tea-series/strawbery-tea.svg',
            ],
            [
                'category_slug' => 'tea',
                'series_title' => 'AUTHENTIC TEA SERIES',
                'name' => 'Lemon Tea',
                'short_description' => 'Original tea dengan syrup lemon dan bahan pelengkap lain yang bikin tenggorokan seger.',
                'price' => 5000,
                'image_url' => '/assets/tea-series/lemon-tea.svg',
            ],
            [
                'category_slug' => 'tea',
                'series_title' => 'AUTHENTIC TEA SERIES',
                'name' => 'Pure Lemon Tea',
                'short_description' => 'Original tea dengan potongan lemon buah segar.',
                'price' => 7000,
                'image_url' => '/assets/tea-series/pure-tea.svg',
            ],

            // ================= YAKULT =================
            [
                'category_slug' => 'yakult',
                'series_title' => 'YAKULT SERIES',
                'name' => 'Lychee Yakult',
                'short_description' => 'Basic yakult segar dikombinasikan syrup buah dan krimer, bikin hari makin fresh.',
                'price' => 8000,
                'image_url' => '/assets/yakult-series/lychee-yakult.svg',
            ],
            [
                'category_slug' => 'yakult',
                'series_title' => 'YAKULT SERIES',
                'name' => 'Manggo Yakult',
                'short_description' => 'Basic yakult segar dikombinasikan syrup mangga dan krimer, bikin hari makin fresh.',
                'price' => 8000,
                'image_url' => '/assets/yakult-series/mangga-yakult.svg',
            ],
            [
                'category_slug' => 'yakult',
                'series_title' => 'YAKULT SERIES',
                'name' => 'Orange Yakult',
                'short_description' => 'Basic yakult segar dikombinasikan syrup jeruk dan krimer, bikin hari makin fresh.',
                'price' => 8000,
                'image_url' => '/assets/yakult-series/orange-yakult.svg',
            ],
            [
                'category_slug' => 'yakult',
                'series_title' => 'YAKULT SERIES',
                'name' => 'Sirsak Yakult',
                'short_description' => 'Basic yakult segar dikombinasikan syrup sirsak dan krimer, bikin hari makin fresh.',
                'price' => 8000,
                'image_url' => '/assets/yakult-series/sirsak-yakult.svg',
            ],
            [
                'category_slug' => 'yakult',
                'series_title' => 'YAKULT SERIES',
                'name' => 'Strawberry Yakult',
                'short_description' => 'Basic yakult segar dikombinasikan syrup strawberry dan krimer, bikin hari makin fresh.',
                'price' => 8000,
                'image_url' => '/assets/yakult-series/strawberry-yakult.svg',
            ],
            [
                'category_slug' => 'yakult',
                'series_title' => 'YAKULT SERIES',
                'name' => 'Melon Yakult',
                'short_description' => 'Basic yakult segar dikombinasikan syrup melon dan krimer, bikin hari makin fresh.',
                'price' => 8000,
                'image_url' => '/assets/yakult-series/melon-yakult.svg',
            ],

            // ================= MILKSHAKE =================
            [
                'category_slug' => 'milkshake',
                'series_title' => 'MILKSHAKE SERIES',
                'name' => 'Dark Choco',
                'short_description' => 'Susu berkualitas yang dishake dengan bubuk perasa, bikin hari lebih berwarna.',
                'price' => 10000,
                'image_url' => '/assets/milkshake/dark_choco.svg',
            ],
            [
                'category_slug' => 'milkshake',
                'series_title' => 'MILKSHAKE SERIES',
                'name' => 'Bubble Gum',
                'short_description' => 'Susu berkualitas dishake dengan bubuk perasa bubble gum.',
                'price' => 10000,
                'image_url' => '/assets/milkshake/bubble_gum.svg',
            ],
            [
                'category_slug' => 'milkshake',
                'series_title' => 'MILKSHAKE SERIES',
                'name' => 'Taro',
                'short_description' => 'Susu berkualitas dishake dengan bubuk perasa taro.',
                'price' => 10000,
                'image_url' => '/assets/milkshake/taro.svg',
            ],
            [
                'category_slug' => 'milkshake',
                'series_title' => 'MILKSHAKE SERIES',
                'name' => 'Thai Tea',
                'short_description' => 'Susu berkualitas dishake dengan bubuk perasa thai tea.',
                'price' => 10000,
                'image_url' => '/assets/milkshake/thai_tea.svg',
            ],
            [
                'category_slug' => 'milkshake',
                'series_title' => 'MILKSHAKE SERIES',
                'name' => 'Matcha',
                'short_description' => 'Susu berkualitas dishake dengan bubuk perasa matcha.',
                'price' => 10000,
                'image_url' => '/assets/milkshake/matcha.svg',
            ],
            [
                'category_slug' => 'milkshake',
                'series_title' => 'MILKSHAKE SERIES',
                'name' => 'Red Velvet',
                'short_description' => 'Susu berkualitas dishake dengan bubuk perasa red velvet.',
                'price' => 10000,
                'image_url' => '/assets/milkshake/red_velvet.svg',
            ],
            [
                'category_slug' => 'milkshake',
                'series_title' => 'MILKSHAKE SERIES',
                'name' => 'Black Forest',
                'short_description' => 'Susu berkualitas dishake dengan bubuk perasa black forest.',
                'price' => 10000,
                'image_url' => '/assets/milkshake/black_forest.svg',
            ],

            // ================= COFFEE =================
            [
                'category_slug' => 'coffee',
                'series_title' => 'COFFEE SERIES',
                'name' => 'Espresso',
                'short_description' => 'Kopi pilihan dipadukan dengan es batu kristal, bikin hari lebih santai.',
                'price' => 13000,
                'image_url' => '/assets/coffee/espresso.svg',
            ],
            [
                'category_slug' => 'coffee',
                'series_title' => 'COFFEE SERIES',
                'name' => 'Moccacino',
                'short_description' => 'Kopi pilihan dipadukan dengan es batu kristal, bikin hari lebih santai.',
                'price' => 13000,
                'image_url' => '/assets/coffee/moccacino.svg',
            ],
            [
                'category_slug' => 'coffee',
                'series_title' => 'COFFEE SERIES',
                'name' => 'Tiramisu',
                'short_description' => 'Kopi pilihan dipadukan dengan es batu kristal, bikin hari lebih santai.',
                'price' => 13000,
                'image_url' => '/assets/coffee/tiramisu.svg',
            ],

            // ================= CHEESE =================
            [
                'category_slug' => 'cheese',
                'series_title' => 'CHEESE CREAM SERIES',
                'name' => 'Matcha Cheese',
                'short_description' => 'Susu berkualitas dicampur bubuk perasa, dipadukan dengan cheese cream premium.',
                'price' => 16000,
                'image_url' => '/assets/cheese/matcha_cheese.svg',
            ],
            [
                'category_slug' => 'cheese',
                'series_title' => 'CHEESE CREAM SERIES',
                'name' => 'Chocolate Cheese',
                'short_description' => 'Susu berkualitas dicampur bubuk perasa coklat, dipadukan dengan cheese cream premium.',
                'price' => 16000,
                'image_url' => '/assets/cheese/chocolate_cheese.svg',
            ],
            [
                'category_slug' => 'cheese',
                'series_title' => 'CHEESE CREAM SERIES',
                'name' => 'Red Velvet Cheese',
                'short_description' => 'Susu berkualitas dicampur bubuk perasa red velvet, dipadukan dengan cheese cream premium.',
                'price' => 16000,
                'image_url' => '/assets/cheese/red_velvet_cheese.svg',
            ],
            [
                'category_slug' => 'cheese',
                'series_title' => 'CHEESE CREAM SERIES',
                'name' => 'Taro Cheese',
                'short_description' => 'Susu berkualitas dicampur bubuk perasa taro, dipadukan dengan cheese cream premium.',
                'price' => 16000,
                'image_url' => '/assets/cheese/taro_cheese.svg',
            ],
            [
                'category_slug' => 'cheese',
                'series_title' => 'CHEESE CREAM SERIES',
                'name' => 'Thai Tea Cheese',
                'short_description' => 'Susu berkualitas dicampur bubuk perasa thai tea, dipadukan dengan cheese cream premium.',
                'price' => 16000,
                'image_url' => '/assets/cheese/thai_tea_cheese.svg',
            ],
        ];

        foreach ($products as $p) {
            $slug = Str::slug($p['name']);

            DB::table('products')->updateOrInsert(
                ['slug' => $slug],
                [
                    'category_id' => $catId($p['category_slug']),
                    'series_title' => $p['series_title'],
                    'name' => $p['name'],
                    'short_description' => $p['short_description'],
                    'price' => $p['price'],
                    'image_url' => $p['image_url'],
                    'gofood_url' => $gofood,
                    'grabfood_url' => $grab,
                    'shopeefood_url' => $shopee,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
