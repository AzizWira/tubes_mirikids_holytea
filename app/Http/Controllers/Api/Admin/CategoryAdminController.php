<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryAdminController extends Controller
{
    public function index()
    {
        return response()->json(['stub' => true], 200);
    }
    public function store(Request $request)
    {
        return response()->json(['stub' => true], 200);
    }
    public function show(string $id)
    {
        return response()->json(['stub' => true], 200);
    }
    public function update(Request $request, string $id)
    {
        return response()->json(['stub' => true], 200);
    }
    public function destroy(string $id)
    {
        return response()->json(['stub' => true], 200);
    }
}
