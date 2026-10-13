<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class SiteAssetsController extends Controller
{
    private const CSS_PATH = 'resources/css/site.css';

    private const JS_PATH = 'resources/js/site.js';

    public function edit()
    {
        $publishedCss = File::get(base_path(self::CSS_PATH));
        $publishedJs = File::get(base_path(self::JS_PATH));

        $draftCss = SiteSetting::get('site_css_draft') ?? $publishedCss;
        $draftJs = SiteSetting::get('site_js_draft') ?? $publishedJs;

        return view('admin.site-assets.edit', [
            'css' => $draftCss,
            'js' => $draftJs,
            'hasUnpublishedChanges' => $draftCss !== $publishedCss || $draftJs !== $publishedJs,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'css' => 'required|string',
            'js' => 'required|string',
        ]);

        SiteSetting::setMany([
            'site_css_draft' => $validated['css'],
            'site_js_draft' => $validated['js'],
        ]);

        return redirect()
            ->route('admin.site-assets.edit')
            ->with('status', 'Taslak kaydedildi. Siteye yansıması için "Yayınla" butonuna basmanız gerekir.');
    }

    public function publish(Request $request)
    {
        $validated = $request->validate([
            'css' => 'required|string',
            'js' => 'required|string',
        ]);

        SiteSetting::setMany([
            'site_css_draft' => $validated['css'],
            'site_js_draft' => $validated['js'],
        ]);

        $cssPath = base_path(self::CSS_PATH);
        $jsPath = base_path(self::JS_PATH);

        $previousCss = File::get($cssPath);
        $previousJs = File::get($jsPath);

        File::put($cssPath, $validated['css']);
        File::put($jsPath, $validated['js']);

        $result = Process::path(base_path())->timeout(180)->run('npm run build');

        if (! $result->successful()) {
            File::put($cssPath, $previousCss);
            File::put($jsPath, $previousJs);

            return back()
                ->withInput()
                ->withErrors([
                    'build' => 'Derleme başarısız oldu, değişiklikler siteye yansımadı ve dosyalar eski haline geri alındı. Hata: '.trim($result->errorOutput() ?: $result->output()),
                ]);
        }

        return redirect()
            ->route('admin.site-assets.edit')
            ->with('status', 'Yayınlandı — değişiklikler derlendi ve siteye yansıdı.');
    }

    public function discard()
    {
        SiteSetting::setMany([
            'site_css_draft' => File::get(base_path(self::CSS_PATH)),
            'site_js_draft' => File::get(base_path(self::JS_PATH)),
        ]);

        return redirect()
            ->route('admin.site-assets.edit')
            ->with('status', 'Taslak, yayındaki haliyle sıfırlandı.');
    }
}
