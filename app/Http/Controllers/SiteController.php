<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\Winner;
use App\Services\Cms\CssScoper;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home(Request $request)
    {
        if (SiteSetting::get('homepage_override_enabled') === '1') {
            return view('site.home-override', [
                'html' => SiteSetting::get('homepage_override_html', ''),
                'css' => SiteSetting::get('homepage_override_css'),
                'js' => SiteSetting::get('homepage_override_js'),
            ]);
        }

        $settings = SiteSetting::getMany(array_keys(SiteSetting::HOMEPAGE_DEFAULTS));

        foreach ($settings as $key => $value) {
            $settings[$key] = $value ?? SiteSetting::HOMEPAGE_DEFAULTS[$key];
        }

        $sections = HomeSection::where('status', true)
            ->orderBy('sort_order')
            ->get();

        $preview = $request->boolean('preview') && auth()->check();

        $settings['hero_emblem'] = SiteSetting::get('hero_emblem');

        return view('site.home', array_merge($settings, ['sections' => $sections, 'preview' => $preview]));
    }

    public function sitemap()
    {
        $pages = Page::where('status', true)
            ->where('robots_index', true)
            ->get();

        return response()
            ->view('sitemap', compact('pages'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /admin/',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/plain');
    }

    public function page(Request $request, $slug)
    {
        if (str_ends_with($request->getPathInfo(), '/')) {
            $canonicalUrl = route('site.page', $slug);
            $queryString = $request->getQueryString();

            return redirect($queryString ? $canonicalUrl.'?'.$queryString : $canonicalUrl, 301);
        }

        $isPreview = $request->boolean('preview') && $request->has('custom_html') && auth()->check();

        $pageQuery = Page::where('slug', $slug);

        if (! $isPreview) {
            $pageQuery->where('status', true);
        }

        $page = $pageQuery->firstOrFail();

        $previewHtml = $isPreview ? (string) $request->input('custom_html') : null;
        $previewCss = $isPreview ? app(CssScoper::class)->scope($request->input('custom_css')) : null;

        $breadcrumbs = $this->pageBreadcrumbs($page);
        $breadcrumbSchema = $this->breadcrumbSchema($breadcrumbs);

        if ($page->slug === 'categories') {
            $families = $page->activeChildren()->get();

            $familyAwards = [];

            foreach ($families as $family) {
                $familyAwards[$family->id] = $family->activeChildren()->get();
            }

            return view('site.categories-index', compact('page', 'families', 'familyAwards', 'breadcrumbs', 'breadcrumbSchema'));
        }

        if ($page->slug === 'winners') {
            $winners = Winner::where('status', true)
                ->whereHas('category', fn ($query) => $query->where('status', true))
                ->with('category')
                ->orderByDesc('won_at')
                ->get();

            return view('site.winners', compact('page', 'winners', 'breadcrumbs', 'breadcrumbSchema'));
        }

        if ($page->parent && $page->parent->slug === 'categories') {
            $awards = $page->activeChildren()->get();

            $siblingFamilies = $page->parent->activeChildren()
                ->where('id', '!=', $page->id)
                ->get();

            return view('site.category-family', compact('page', 'awards', 'siblingFamilies', 'breadcrumbs', 'breadcrumbSchema'));
        }

        $family = null;
        $siblingAwards = collect();

        if ($page->parent && $page->parent->parent && $page->parent->parent->slug === 'categories') {
            $family = $page->parent;

            $siblingAwards = $family->activeChildren()
                ->where('id', '!=', $page->id)
                ->get();
        }

        return view('site.page', compact('page', 'breadcrumbs', 'breadcrumbSchema', 'previewHtml', 'previewCss', 'family', 'siblingAwards'));
    }

    public function verify(string $slug)
    {
        $winner = Winner::where('verification_slug', $slug)
            ->where('status', true)
            ->with('category')
            ->firstOrFail();

        $winnersPage = Page::where('slug', 'winners')->where('status', true)->first();

        return response()
            ->view('site.verify', compact('winner', 'winnersPage'))
            ->header('X-Frame-Options', 'DENY');
    }

    private function pageBreadcrumbs(Page $page): array
    {
        $chain = [];
        $node = $page;

        while ($node) {
            $chain[] = $node;
            $node = $node->parent;
        }

        $breadcrumbs = [];

        foreach (array_reverse($chain) as $ancestor) {
            $breadcrumbs[] = (object) [
                'title' => $ancestor->title,
                'url' => $ancestor->publicUrl(),
            ];
        }

        return $breadcrumbs;
    }

    private function breadcrumbSchema(array $breadcrumbs): array
    {
        $listItems = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => route('home'),
            ],
        ];

        foreach ($breadcrumbs as $breadcrumb) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => count($listItems) + 1,
                'name' => $breadcrumb->title,
                'item' => $breadcrumb->url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }
}
