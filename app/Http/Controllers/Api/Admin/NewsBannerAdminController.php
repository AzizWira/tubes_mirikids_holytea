<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsBanner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsBannerAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = NewsBanner::orderBy('sort_order')->orderByDesc('id')->get();
        return response()->json($banners);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $banner = NewsBanner::create($data);

        return response()->json($banner, 201);
    }

    public function show(int $id): JsonResponse
    {
        $banner = NewsBanner::findOrFail($id);
        return response()->json($banner);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $banner = NewsBanner::findOrFail($id);
        $data = $this->validated($request);
        $banner->update($data);

        return response()->json($banner);
    }

    public function destroy(int $id): JsonResponse
    {
        $banner = NewsBanner::findOrFail($id);
        $banner->delete();

        return response()->json(null, 204);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'      => ['nullable', 'string', 'max:255'],
            'image_url'  => ['required', 'string', 'max:255'],
            'is_active'  => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);
    }
}
