<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function productImage(Request $request)
    {
        return response()->json(['stub' => true], 200);
    }
}
