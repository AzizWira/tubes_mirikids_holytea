<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingAdminController extends Controller
{
    public function show()
    {
        return response()->json(['stub' => true], 200);
    }
    public function update(Request $request)
    {
        return response()->json(['stub' => true], 200);
    }
}
