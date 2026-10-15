<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class FooterMenuController extends Controller
{
    private const LOCATION = 'footer';

    public function index()
    {
        $menuItems = MenuItem::with('page')
            ->where('location', self::LOCATION)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $settings = SiteSetting::getMany(SiteSetting::FOOTER_KEYS);

        return view('admin.footer-menu.index', compact('menuItems', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'footer_description' => 'nullable|string|max:500',
            'footer_copyright' => 'nullable|string|max:255',
            'footer_email' => 'nullable|email|max:255',
        ]);

        SiteSetting::setMany($validated);

        return redirect()
            ->route('admin.footer-menu.index')
            ->with('status', 'Footer bilgileri kaydedildi.');
    }

    public function create()
    {
        $pages = Page::orderBy('title')->get();
        $parentItems = MenuItem::where('location', self::LOCATION)->orderBy('label')->get();

        return view('admin.footer-menu.create', compact('pages', 'parentItems'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenuItem($request);
        $validated['location'] = self::LOCATION;

        MenuItem::create($validated);

        return redirect()->route('admin.footer-menu.index');
    }

    public function edit($id)
    {
        $menuItem = MenuItem::where('location', self::LOCATION)->findOrFail($id);

        $pages = Page::orderBy('title')->get();

        $excludedIds = array_merge([$menuItem->id], $menuItem->descendantIds());

        $parentItems = MenuItem::where('location', self::LOCATION)
            ->whereNotIn('id', $excludedIds)
            ->orderBy('label')
            ->get();

        return view('admin.footer-menu.edit', compact('menuItem', 'pages', 'parentItems'));
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::where('location', self::LOCATION)->findOrFail($id);

        $forbiddenParentIds = array_merge([$menuItem->id], $menuItem->descendantIds());

        $validated = $this->validateMenuItem($request, $forbiddenParentIds);

        $menuItem->update($validated);

        return redirect()->route('admin.footer-menu.index');
    }

    public function destroy($id)
    {
        MenuItem::where('location', self::LOCATION)->findOrFail($id)->delete();

        return redirect()->route('admin.footer-menu.index');
    }

    private function validateMenuItem(Request $request, array $forbiddenParentIds = []): array
    {
        $validated = $request->validate(MenuItem::validationRules(self::LOCATION, $forbiddenParentIds));

        $validated['open_in_new_tab'] = $request->boolean('open_in_new_tab');

        return $validated;
    }
}
