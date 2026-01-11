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

    public function newsBannerImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:2048'], // maks 2MB
        ]);

        $file = $request->file('file');
        $name = 'news_' . Str::random(12) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('news-banners', $name, 'public');

        return response()->json([
            'url' => asset('storage/' . $path),
            'path' => $path,
        ], 201);
    }
}
