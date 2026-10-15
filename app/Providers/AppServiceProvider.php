<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\SiteSetting;
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
            $view->with('siteChrome', SiteSetting::getMany(array_merge(SiteSetting::HEADER_KEYS, SiteSetting::FOOTER_KEYS)));
        });

        View::composer(['layouts.admin', 'admin.auth.login'], function ($view) {
            $view->with('siteLogo', SiteSetting::get('site_logo'));
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
