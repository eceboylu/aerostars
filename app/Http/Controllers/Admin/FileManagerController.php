<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public static function uploadedImageRules(): array
    {
        return [
            'bail',
            'nullable',
            'string',
            'max:2048',
            function (string $attribute, mixed $value, Closure $fail): void {
                if (! preg_match('/^\/uploads\/[A-Za-z0-9._-]+\.(png|jpe?g|webp)$/i', $value)) {
                    $fail('Görsel, dosya yöneticisinden seçilmiş bir PNG, JPG veya WebP dosyası olmalıdır.');

                    return;
                }

                if (! Storage::disk('uploads')->exists(basename($value))) {
                    $fail('Seçilen görsel dosyası bulunamadı. Lütfen dosya yöneticisinden yeniden seçin.');
                }
            },
        ];
    }

    public function index(Request $request)
    {
        $disk = Storage::disk('uploads');

        if (! $disk->exists('/')) {
            $disk->makeDirectory('/');
        }

        $files = collect($disk->files())
            ->filter(fn ($path) => in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::ALLOWED_EXTENSIONS, true))
            ->sortByDesc(fn ($path) => $disk->lastModified($path))
            ->map(fn ($path) => [
                'name' => basename($path),
                'url' => $disk->url($path),
                'size' => $disk->size($path),
            ])
            ->values();

        $picker = $request->boolean('picker');

        $view = $picker ? 'admin.filemanager.picker' : 'admin.filemanager.index';

        return view($view, compact('files', 'picker'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $image = $request->file('image');
        $disk = Storage::disk('uploads');
        $slug = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
        $slug = $slug ?: 'image';
        $extension = strtolower($image->getClientOriginalExtension());

        $filename = $slug.'.'.$extension;
        $attempt = 1;

        while ($disk->exists($filename)) {
            $attempt++;
            $filename = $slug.'-'.$attempt.'.'.$extension;
        }

        $image->storeAs('/', $filename, 'uploads');

        return response()->json([
            'path' => $disk->url($filename),
        ]);
    }

    public function destroy(string $filename)
    {
        $filename = basename($filename);
        $disk = Storage::disk('uploads');
        $deleted = false;

        if (in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), self::ALLOWED_EXTENSIONS, true) && $disk->exists($filename)) {
            $deleted = $disk->delete($filename);
        }

        return response()->json(['deleted' => $deleted]);
    }
}
