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
    try {
        $exists = DB::table('products')->where('slug', $slug)->exists();
        abort_if(!$exists, 404);
    } catch (\Throwable $e) {
        abort(404);
    }

    return view('user.detail', compact('slug'));
})->name('user.detail');

/*
|--------------------------------------------------------------------------
| ADMIN PAGES
|--------------------------------------------------------------------------
*/
Route::view('/login', 'admin.login')->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::view('/products', 'admin.products.index')->name('admin.products.index');
    Route::view('/categories', 'admin.categories.index')->name('admin.categories.index');

    Route::view('/testimonials', 'admin.testimonials.index')->name('admin.testimonials.index');
    Route::view('/settings', 'admin.settings.index')->name('admin.settings.index');
});

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
