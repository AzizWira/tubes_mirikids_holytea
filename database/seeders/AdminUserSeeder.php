<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN 1
        DB::table('users')->updateOrInsert(
            ['username' => 'admin1'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ADMIN 2
        DB::table('users')->updateOrInsert(
            ['username' => 'admin2'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin2',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
