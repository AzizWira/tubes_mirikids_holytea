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

        $ext = $file->getClientOriginalExtension();
        $name = Str::uuid()->toString() . '.' . $ext;

        // simpan ke storage/app/public/products
        $path = $file->storeAs('products', $name, 'public');

        // url publik (butuh php artisan storage:link)
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
