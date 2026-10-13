<?php

namespace App\Providers;

use App\Models\MenuItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.site', function ($view) {
            $view->with('headerMenuItems', $this->menuTree('header'));
            $view->with('footerMenuItems', $this->menuTree('footer'));
        });
    }

    private function menuTree(string $location)
    {
        return MenuItem::with('page', 'children.page')
            ->where('location', $location)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }
}
