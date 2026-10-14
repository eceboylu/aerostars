<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\FileManagerController;
use App\Http\Controllers\Admin\FooterMenuController;
use App\Http\Controllers\Admin\HeaderMenuController;
use App\Http\Controllers\Admin\HomepageOverrideController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SiteAssetsController;
use App\Http\Controllers\Admin\WinnerController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])
    ->name('home');

Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])
    ->name('sitemap');

Route::get('/robots.txt', [SiteController::class, 'robots'])
    ->name('robots');

Route::get('/verify/{slug}', [SiteController::class, 'verify'])
    ->name('site.verify');

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('admin.login.attempt');

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin', function () {
        return redirect()->route('admin.pages.dashboard');
    })->name('admin.home');

    Route::get('/admin/dashboard', [PageController::class, 'dashboard'])
        ->name('admin.pages.dashboard');

    Route::get('/admin/pages', [PageController::class, 'index'])
        ->name('admin.pages.index');

    Route::get('/admin/pages/create', [PageController::class, 'create'])
        ->name('admin.pages.create');

    Route::post('/admin/pages', [PageController::class, 'store'])
        ->name('admin.pages.store');

    Route::post('/admin/pages/preview', [PageController::class, 'preview'])
        ->name('admin.pages.preview');

    Route::get('/admin/pages/{page}/edit', [PageController::class, 'edit'])
        ->name('admin.pages.edit');

    Route::put('/admin/pages/{page}', [PageController::class, 'update'])
        ->name('admin.pages.update');

    Route::delete('/admin/pages/{page}', [PageController::class, 'destroy'])
        ->name('admin.pages.destroy');

    Route::get('/admin/header-menu', [HeaderMenuController::class, 'index'])
        ->name('admin.header-menu.index');

    Route::get('/admin/header-menu/create', [HeaderMenuController::class, 'create'])
        ->name('admin.header-menu.create');

    Route::post('/admin/header-menu', [HeaderMenuController::class, 'store'])
        ->name('admin.header-menu.store');

    Route::get('/admin/header-menu/{headerMenuItem}/edit', [HeaderMenuController::class, 'edit'])
        ->name('admin.header-menu.edit');

    Route::put('/admin/header-menu/{headerMenuItem}', [HeaderMenuController::class, 'update'])
        ->name('admin.header-menu.update');

    Route::delete('/admin/header-menu/{headerMenuItem}', [HeaderMenuController::class, 'destroy'])
        ->name('admin.header-menu.destroy');

    Route::get('/admin/footer-menu', [FooterMenuController::class, 'index'])
        ->name('admin.footer-menu.index');

    Route::get('/admin/footer-menu/create', [FooterMenuController::class, 'create'])
        ->name('admin.footer-menu.create');

    Route::post('/admin/footer-menu', [FooterMenuController::class, 'store'])
        ->name('admin.footer-menu.store');

    Route::get('/admin/footer-menu/{footerMenuItem}/edit', [FooterMenuController::class, 'edit'])
        ->name('admin.footer-menu.edit');

    Route::put('/admin/footer-menu/{footerMenuItem}', [FooterMenuController::class, 'update'])
        ->name('admin.footer-menu.update');

    Route::delete('/admin/footer-menu/{footerMenuItem}', [FooterMenuController::class, 'destroy'])
        ->name('admin.footer-menu.destroy');

    Route::get('/admin/home-sections', [HomeSectionController::class, 'index'])
        ->name('admin.home-sections.index');

    Route::get('/admin/home-sections/create', [HomeSectionController::class, 'create'])
        ->name('admin.home-sections.create');

    Route::post('/admin/home-sections', [HomeSectionController::class, 'store'])
        ->name('admin.home-sections.store');

    Route::get('/admin/home-sections/{homeSection}/edit', [HomeSectionController::class, 'edit'])
        ->name('admin.home-sections.edit');

    Route::put('/admin/home-sections/{homeSection}', [HomeSectionController::class, 'update'])
        ->name('admin.home-sections.update');

    Route::delete('/admin/home-sections/{homeSection}', [HomeSectionController::class, 'destroy'])
        ->name('admin.home-sections.destroy');

    Route::post('/admin/home-sections/{homeSection}/move-up', [HomeSectionController::class, 'moveUp'])
        ->name('admin.home-sections.move-up');

    Route::post('/admin/home-sections/{homeSection}/move-down', [HomeSectionController::class, 'moveDown'])
        ->name('admin.home-sections.move-down');

    Route::post('/admin/home-sections/{homeSection}/publish', [HomeSectionController::class, 'publish'])
        ->name('admin.home-sections.publish');

    Route::post('/admin/home-sections/{homeSection}/revert', [HomeSectionController::class, 'revert'])
        ->name('admin.home-sections.revert');

    Route::get('/admin/homepage-override', [HomepageOverrideController::class, 'edit'])
        ->name('admin.homepage-override.edit');

    Route::put('/admin/homepage-override', [HomepageOverrideController::class, 'update'])
        ->name('admin.homepage-override.update');

    Route::post('/admin/homepage-override/preview', [HomepageOverrideController::class, 'preview'])
        ->name('admin.homepage-override.preview');

    Route::get('/admin/settings/assets', [SiteAssetsController::class, 'edit'])
        ->name('admin.site-assets.edit');

    Route::post('/admin/settings/assets', [SiteAssetsController::class, 'update'])
        ->name('admin.site-assets.update');

    Route::post('/admin/settings/assets/publish', [SiteAssetsController::class, 'publish'])
        ->name('admin.site-assets.publish');

    Route::post('/admin/settings/assets/discard', [SiteAssetsController::class, 'discard'])
        ->name('admin.site-assets.discard');

    Route::get('/admin/filemanager', [FileManagerController::class, 'index'])
        ->name('admin.filemanager.index');

    Route::post('/admin/filemanager/upload', [FileManagerController::class, 'upload'])
        ->name('admin.filemanager.upload');

    Route::delete('/admin/filemanager/{filename}', [FileManagerController::class, 'destroy'])
        ->where('filename', '.*')
        ->name('admin.filemanager.destroy');

    Route::get('/admin/winners', [WinnerController::class, 'index'])
        ->name('admin.winners.index');

    Route::get('/admin/winners/create', [WinnerController::class, 'create'])
        ->name('admin.winners.create');

    Route::post('/admin/winners', [WinnerController::class, 'store'])
        ->name('admin.winners.store');

    Route::get('/admin/winners/{winner}/edit', [WinnerController::class, 'edit'])
        ->name('admin.winners.edit');

    Route::put('/admin/winners/{winner}', [WinnerController::class, 'update'])
        ->name('admin.winners.update');

    Route::delete('/admin/winners/{winner}', [WinnerController::class, 'destroy'])
        ->name('admin.winners.destroy');
});

Route::match(['get', 'post', 'put'], '/{slug}', [SiteController::class, 'page'])
    ->where('slug', '.*')
    ->name('site.page');
