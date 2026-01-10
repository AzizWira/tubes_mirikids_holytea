<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function productImage(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
        ]);

        $file = $data['image'];

        // nama file unik
        $name = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();

        // simpan ke storage/app/public/products
        $path = $file->storeAs('products', $name, 'public');

        // url publik
        $url = '/storage/' . $path;

        return response()->json([
            'success' => true,
            'data' => [
                'path' => $path, // products/uuid.webp
                'url' => $url,   // /storage/products/uuid.webp
            ],
        ], 201);
    }
}
