<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingAdminController extends Controller
{
    // daftar key yang kita izinkan (biar aman)
    private array $allowedKeys = [
        'maps_url',
        'maps_embed_url',
        'address',
        'open_days',
        'open_hours',
        'friday_hours',
        'phone',
        'email',
        'instagram_url',
    ];

    public function show()
    {
        $rows = DB::table('site_settings')
            ->select(['key', 'value'])
            ->whereIn('key', $this->allowedKeys)
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $out[$r->key] = $r->value;
        }

        // pastikan semua key selalu ada (biar FE enak)
        foreach ($this->allowedKeys as $k) {
            if (!array_key_exists($k, $out))
                $out[$k] = null;
        }

        return response()->json([
            'success' => true,
            'data' => $out
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'maps_url' => ['nullable', 'string', 'max:255'],
            'maps_embed_url' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'open_days' => ['nullable', 'string', 'max:80'],
            'open_hours' => ['nullable', 'string', 'max:80'],
            'friday_hours' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'max:100'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($this->allowedKeys as $key) {
            if (!array_key_exists($key, $data))
                continue;

            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $data[$key],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return $this->show();
    }
}
