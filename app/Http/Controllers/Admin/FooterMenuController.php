<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use App\Services\Cms\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        return view('admin.footer-menu.index', compact('menuItems'));
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
        $validated = $request->validate([
            'parent_id' => [
                'nullable',
                Rule::exists('menu_items', 'id')->where(fn ($query) => $query->where('location', self::LOCATION)),
                Rule::notIn($forbiddenParentIds),
            ],
            'link_type' => 'required|in:page,custom,none',
            'page_id' => 'required_if:link_type,page|nullable|exists:pages,id',
            'custom_url' => [
                'required_if:link_type,custom',
                'nullable',
                'string',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if ($value && ! app(HtmlSanitizer::class)->isSafeUrl($value)) {
                        $fail('Geçersiz veya güvenli olmayan bir adres girdiniz.');
                    }
                },
            ],
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        $validated['open_in_new_tab'] = $request->boolean('open_in_new_tab');

        return $validated;
    }
}
