<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ProductController;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\ProductAdminController;
use App\Http\Controllers\Api\Admin\CategoryAdminController;
use App\Http\Controllers\Api\Admin\TestimonialAdminController;
use App\Http\Controllers\Api\Admin\SettingAdminController;
use App\Http\Controllers\Api\Admin\UploadController;

/*
|--------------------------------------------------------------------------
| PUBLIC API (USER)
|--------------------------------------------------------------------------
*/

Route::get('/home', [HomeController::class, 'index']);
Route::get('/menu', [MenuController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

/*
|--------------------------------------------------------------------------
| AUTH API (ADMIN)
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED API (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------
    | ADMIN 1 (PRODUCT & CATEGORY)
    |--------------------------
    */
    Route::prefix('admin')->middleware('role:admin1')->group(function () {
        Route::post('/upload/product-image', [UploadController::class, 'productImage']);
        Route::apiResource('products', ProductAdminController::class);
        Route::apiResource('categories', CategoryAdminController::class);
    });

    /*
    |--------------------------
    | ADMIN 2 (TESTIMONIAL & SETTINGS)
    |--------------------------
    */
    Route::prefix('admin')->middleware('role:admin2')->group(function () {

        // dropdown pilihan produk (untuk modal tambah/edit testimoni)
        Route::get('/products-options', [TestimonialAdminController::class, 'productOptions']);

        // testimoni (CRUD)
        Route::apiResource('testimonials', TestimonialAdminController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        // settings
        Route::get('/settings', [SettingAdminController::class, 'show']);
        Route::put('/settings', [SettingAdminController::class, 'update']);
    });
});
