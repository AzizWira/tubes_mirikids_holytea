<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| USER (PUBLIC) PAGES
|--------------------------------------------------------------------------
*/

Route::view('/', 'user.index')->name('user.home');
Route::view('/menu', 'user.menu')->name('user.menu');

Route::get('/detail/{slug}', function (string $slug) {
    $exists = DB::table('products')
        ->where('slug', $slug)
        ->where('is_active', 1)
        ->exists();

    abort_if(!$exists, 404);

    return view('user.detail', compact('slug'));
})->name('user.detail');

/*
|--------------------------------------------------------------------------
| ADMIN PAGES (API-FIRST)
|--------------------------------------------------------------------------
*/
Route::view('/login', 'admin.login')
    ->name('login')
    ->middleware('guest'); // biar kalau sudah "session login" (kalau ada) gak balik lagi

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Admin1 pages (proteksi via JS guard di admin.layout)
    Route::view('/products', 'admin.products.index')->name('admin.products.index');
    Route::view('/categories', 'admin.categories.index')->name('admin.categories.index');
    Route::view('/news-banners', 'admin.news-banners.index')->name('admin.news-banners.index');

    // Admin2 pages (proteksi via JS guard di admin.layout)
    Route::view('/testimonials', 'admin.testimonials.index')->name('admin.testimonials.index');
    Route::view('/settings', 'admin.settings.index')->name('admin.settings');
});

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
