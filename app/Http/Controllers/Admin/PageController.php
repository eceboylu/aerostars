<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Services\Cms\CssScoper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class PageController extends Controller
{
    public function dashboard()
    {
        $categoriesRootPage = Page::where('slug', 'categories')->first();
        $familyCount = 0;
        $categoryCount = 0;

        if ($categoriesRootPage) {
            $familyPages = $categoriesRootPage->children()->withCount('children')->get();
            $familyCount = $familyPages->count();
            $categoryCount = $familyPages->sum('children_count');
        }

        return view('admin.pages.dashboard', [
            'pageTotal' => Page::count(),
            'pageActive' => Page::where('status', true)->count(),
            'familyCount' => $familyCount,
            'categoryCount' => $categoryCount,
            'sectionTotal' => HomeSection::count(),
            'sectionActive' => HomeSection::where('status', true)->count(),
            'sectionDrafts' => HomeSection::all()->filter(fn ($section) => $section->hasUnpublishedChanges())->count(),
            'overrideEnabled' => SiteSetting::get('homepage_override_enabled') === '1',
            'headerMenuCount' => MenuItem::where('location', 'header')->count(),
            'footerMenuCount' => MenuItem::where('location', 'footer')->count(),
        ]);
    }

    private function recompileCmsAssets(): void
    {
        try {
            Artisan::call('pages:compile-cms-assets');
        } catch (Throwable $e) {
            Log::warning('CMS Tailwind recompile failed: '.$e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $parent = null;

        if ($request->filled('parent_id')) {
            $parent = Page::findOrFail($request->integer('parent_id'));
        }

        $pages = Page::withCount('children')
            ->when($parent, fn ($query) => $query->where('parent_id', $parent->id), fn ($query) => $query->whereNull('parent_id'))
            ->orderBy('title')
            ->get();

        return view('admin.pages.index', compact('pages', 'parent'));
    }

    public function create(Request $request)
    {
        $pages = Page::orderBy('title')->get();
        $presetParentId = $request->integer('parent_id') ?: null;

        return view('admin.pages.create', compact('pages', 'presetParentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:pages,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'status' => 'required|boolean',
            'custom_html' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:2048',
            'robots_index' => 'required|boolean',
            'badge_image' => 'nullable|string|max:2048',
        ]);

        Page::create($validated);

        $this->recompileCmsAssets();

        return redirect()
            ->route('admin.pages.index');
    }

    public function edit(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $excludedPageIds = array_merge(
            [$page->id],
            $page->descendantIds()
        );

        $pages = Page::whereNotIn('id', $excludedPageIds)
            ->orderBy('title')
            ->get();

        $backUrl = $this->resolveBackUrl($request->query('back') ?: url()->previous());

        return view('admin.pages.edit', compact('page', 'pages', 'backUrl'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $forbiddenParentIds = array_merge([$page->id], $page->descendantIds());

        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:pages,id', Rule::notIn($forbiddenParentIds)],
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.$id,
            'custom_html' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'status' => 'required|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:2048',
            'robots_index' => 'required|boolean',
            'badge_image' => 'nullable|string|max:2048',
        ]);

        $page->update($validated);

        $this->recompileCmsAssets();

        $backUrl = $this->resolveBackUrl($request->input('back_url'));

        return redirect()
            ->route('admin.pages.edit', ['page' => $page->id, 'back' => $backUrl])
            ->with('success', 'Sayfa başarıyla güncellendi.');
    }

    private function resolveBackUrl(?string $url): string
    {
        if ($url && str_starts_with($url, url('/admin/pages'))) {
            return $url;
        }

        return route('admin.pages.index');
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'custom_html' => 'nullable|string',
            'custom_css' => 'nullable|string',
        ]);

        return view('admin.pages.preview', [
            'html' => $validated['custom_html'] ?? '',
            'css' => app(CssScoper::class)->scope($validated['custom_css'] ?? null),
        ]);
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);

        $page->delete();
        $this->recompileCmsAssets();

        return redirect()
            ->route('admin.pages.index');
    }
}
