<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingAdminController extends Controller
{
    public function show()
    {
        $row = DB::table('site_settings')->orderBy('id')->first();

        return response()->json([
            'success' => true,
            'data' => $row,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_title' => ['nullable', 'string', 'max:200'],
            'whatsapp' => ['nullable', 'ensure_utf8', 'max:50'],
            'instagram' => ['nullable', 'ensure_utf8', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'about' => ['nullable', 'string'],
        ]);

        $row = DB::table('site_settings')->orderBy('id')->first();

        if (!$row) {
            $id = DB::table('site_settings')->insertGetId(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $row = DB::table('site_settings')->where('id', $id)->first();
        } else {
            DB::table('site_settings')->where('id', $row->id)->update(array_merge($data, [
                'updated_at' => now(),
            ]));
            $row = DB::table('site_settings')->where('id', $row->id)->first();
        }

        return response()->json([
            'success' => true,
            'data' => $row,
        ]);
    }
}
