<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductOptionSeeder::class,
            ProductNutritionSeeder::class,
            TestimonialSeeder::class,
            NewsBannerSeeder::class,
            BestSellerSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}
