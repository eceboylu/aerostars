<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Services\Cms\HtmlSanitizer;
use Illuminate\Http\Request;

class HeaderMenuController extends Controller
{
    private const LOCATION = 'header';

    public function index()
    {
        $menuItems = MenuItem::with('page')
            ->where('location', self::LOCATION)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $settings = SiteSetting::getMany(SiteSetting::HEADER_KEYS);

        return view('admin.header-menu.index', compact('menuItems', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_logo' => FileManagerController::uploadedImageRules(),
            'header_cta_label' => 'nullable|string|max:60|required_with:header_cta_url',
            'header_cta_url' => [
                'nullable',
                'string',
                'max:2048',
                'required_with:header_cta_label',
                function ($attribute, $value, $fail) {
                    if ($value && ! app(HtmlSanitizer::class)->isSafeUrl($value)) {
                        $fail('Geçersiz veya güvenli olmayan bir adres girdiniz.');
                    }
                },
            ],
        ], [
            'header_cta_label.required_with' => 'Bağlantı girildiyse buton yazısı da girilmelidir.',
            'header_cta_url.required_with' => 'Buton yazısı girildiyse bağlantı da girilmelidir.',
        ]);

        SiteSetting::setMany($validated);

        return redirect()
            ->route('admin.header-menu.index')
            ->with('status', 'Header ayarları kaydedildi.');
    }

    public function create()
    {
        $pages = Page::orderBy('title')->get();
        $parentItems = MenuItem::where('location', self::LOCATION)->orderBy('label')->get();

        return view('admin.header-menu.create', compact('pages', 'parentItems'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenuItem($request);
        $validated['location'] = self::LOCATION;

        MenuItem::create($validated);

        return redirect()->route('admin.header-menu.index');
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

        return view('admin.header-menu.edit', compact('menuItem', 'pages', 'parentItems'));
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::where('location', self::LOCATION)->findOrFail($id);

        $forbiddenParentIds = array_merge([$menuItem->id], $menuItem->descendantIds());

        $validated = $this->validateMenuItem($request, $forbiddenParentIds);

        $menuItem->update($validated);

        return redirect()->route('admin.header-menu.index');
    }

    public function destroy($id)
    {
        MenuItem::where('location', self::LOCATION)->findOrFail($id)->delete();

        return redirect()->route('admin.header-menu.index');
    }

    private function validateMenuItem(Request $request, array $forbiddenParentIds = []): array
    {
        $validated = $request->validate(MenuItem::validationRules(self::LOCATION, $forbiddenParentIds));

        $validated['open_in_new_tab'] = $request->boolean('open_in_new_tab');

        return $validated;
    }
}
