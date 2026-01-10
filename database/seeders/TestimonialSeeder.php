<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $sampleProductId = DB::table('products')->where('slug', 'original-tea')->value('id');

        $rows = [
            ['product_id' => $sampleProductId, 'name' => 'Ayu', 'rating' => 5, 'message' => 'Seger banget! Harga ramah di kantong.', 'avatar_url' => null],
            ['product_id' => $sampleProductId, 'name' => 'Bima', 'rating' => 4, 'message' => 'Manisnya pas, cocok buat siang hari.', 'avatar_url' => null],
            ['product_id' => $sampleProductId, 'name' => 'Dina', 'rating' => 5, 'message' => 'Lemon tea favoritku, bikin fresh.', 'avatar_url' => null],
            ['product_id' => null, 'name' => 'Raka', 'rating' => 5, 'message' => 'Pelayanan cepat, minumannya enak semua.', 'avatar_url' => null],
            ['product_id' => null, 'name' => 'Sari', 'rating' => 4, 'message' => 'Best seller-nya mantap!', 'avatar_url' => null],
        ];

        foreach ($rows as $r) {
            DB::table('testimonials')->insert([
                'product_id' => $r['product_id'],
                'name' => $r['name'],
                'rating' => $r['rating'],
                'message' => $r['message'],
                'avatar_url' => $r['avatar_url'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
