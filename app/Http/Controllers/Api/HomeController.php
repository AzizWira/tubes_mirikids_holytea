<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return response()->json(['stub' => true, 'endpoint' => 'home'], 200);
    }
}
