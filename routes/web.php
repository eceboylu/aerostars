<?php
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PageController;

Route::get('/', [SiteController::class, 'home'])
    ->name('home');

Route::get('/about', [SiteController::class, 'about'])
    ->name('about');

    Route::get('/admin/categories', [CategoryController::class, 'index'])
    ->name('admin.categories.index');

    Route::get('/admin/categories/create', [CategoryController::class, 'create'])
    ->name('admin.categories.create');

    Route::post('/admin/categories', [CategoryController::class, 'store'])
    ->name('admin.categories.store');

    Route::get('/admin/categories/{category}/edit', [CategoryController::class, 'edit'])
    ->name('admin.categories.edit');

    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])
    ->name('admin.categories.update');

    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])
    ->name('admin.categories.destroy');

    Route::get('/admin/pages', [PageController::class, 'index'])
    ->name('admin.pages.index');

    Route::get('/admin/pages/create', [PageController::class, 'create'])
    ->name('admin.pages.create');

    Route::post('/admin/pages', [PageController::class, 'store'])
    ->name('admin.pages.store');


    Route::get('/{slug}', [SiteController::class, 'page'])
    ->name('site.page');

    Route::get('/admin/pages/{page}/edit', [PageController::class, 'edit'])
    ->name('admin.pages.edit');

Route::put('/admin/pages/{page}', [PageController::class, 'update'])
    ->name('admin.pages.update');

Route::delete('/admin/pages/{page}', [PageController::class, 'destroy'])
    ->name('admin.pages.destroy');