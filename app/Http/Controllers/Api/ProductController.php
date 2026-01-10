<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        return response()->json(['stub' => true, 'endpoint' => 'product.show', 'slug' => $slug], 200);
    }
}
