<?php

namespace App\Http\Controllers;
use App\Models\Page;


class SiteController extends Controller
{
    public function home()
    {
        return view('site.home');
    }

    public function about()
    {
        return view('site.about');
    }

    public function page($slug)
{
    $page = Page::where('slug', $slug)
        ->where('status', true)
        ->firstOrFail();

    return view('site.page', compact('page'));
}
}