<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomepageOverrideController extends Controller
{
    private const KEYS = [
        'homepage_override_enabled',
        'homepage_override_html',
        'homepage_override_css',
        'homepage_override_js',
    ];

    public function edit()
    {
        $values = SiteSetting::getMany(self::KEYS);

        return view('admin.homepage-override.edit', [
            'enabled' => $values['homepage_override_enabled'] === '1',
            'html' => $values['homepage_override_html'] ?? '',
            'css' => $values['homepage_override_css'] ?? '',
            'js' => $values['homepage_override_js'] ?? '',
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'html' => 'nullable|string',
            'css' => 'nullable|string',
            'js' => 'nullable|string',
        ]);

        SiteSetting::setMany([
            'homepage_override_enabled' => $validated['enabled'] ? '1' : '0',
            'homepage_override_html' => $validated['html'] ?? '',
            'homepage_override_css' => $validated['css'] ?? '',
            'homepage_override_js' => $validated['js'] ?? '',
        ]);

        return redirect()
            ->route('admin.homepage-override.edit')
            ->with('status', 'Saved.');
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'html' => 'nullable|string',
            'css' => 'nullable|string',
            'js' => 'nullable|string',
        ]);

        return view('admin.homepage-override.preview', [
            'html' => $validated['html'] ?? '',
            'css' => $validated['css'] ?? '',
            'js' => $validated['js'] ?? '',
        ]);
    }
}
