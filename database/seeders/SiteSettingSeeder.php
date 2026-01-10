<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'maps_url' => 'https://www.google.com/maps/place/Holy+Tea+Indonesia/',
            'address' => 'Jln Sukun Raya No.9, Besito Kulon, Besito, Kec. Gebog, Kabupaten Kudus, Jawa Tengah 59333',
            'open_days' => 'Senin - Minggu',
            'open_hours' => '10am - 9pm',
            'friday_hours' => '1pm - 9pm',
            'phone' => '+6285172408511',
            'email' => 'Holydrinktea@gmail.com',
            'instagram_url' => 'https://www.instagram.com/holyteaindonesia/',
        ];

        foreach ($settings as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
