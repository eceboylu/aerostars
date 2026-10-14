<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

#[Signature('app:export-static-site {--output=netlify-dist : Output directory relative to the project root}')]
#[Description('Render the public-facing pages into static HTML for a visual-only deploy (e.g. Netlify).')]
class ExportStaticSite extends Command
{
    public function handle(Kernel $kernel): int
    {
        $outputDir = base_path($this->option('output'));

        File::deleteDirectory($outputDir);
        File::makeDirectory($outputDir, 0755, true);

        URL::forceRootUrl('');

        foreach ($this->publicPaths() as $path) {
            $this->renderPath($kernel, $outputDir, $path);
        }

        $this->copyAssets($outputDir);

        $this->info("Static export written to {$outputDir}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function publicPaths(): array
    {
        $paths = ['/'];

        foreach (Page::where('status', true)->get() as $page) {
            $paths[] = '/'.$page->slug;
        }

        return $paths;
    }

    private function renderPath(Kernel $kernel, string $outputDir, string $path): void
    {
        $response = $kernel->handle(Request::create($path, 'GET'));

        if ($response->getStatusCode() !== 200) {
            $this->warn("Skipped {$path} (HTTP {$response->getStatusCode()})");

            return;
        }

        $targetDir = $path === '/' ? $outputDir : $outputDir.$path;

        $html = str_replace(
            ['http://localhost/', 'https://localhost/', 'http://localhost', 'https://localhost'],
            ['/', '/', '', ''],
            $response->getContent()
        );

        File::makeDirectory($targetDir, 0755, true, true);
        File::put($targetDir.'/index.html', $html);

        $this->line("Rendered {$path}");
    }

    private function copyAssets(string $outputDir): void
    {
        foreach (['build', 'uploads'] as $directory) {
            $source = public_path($directory);

            if (File::isDirectory($source)) {
                File::copyDirectory($source, $outputDir.'/'.$directory);
            }
        }

        foreach (['favicon.ico', 'favicon.svg'] as $file) {
            $source = public_path($file);

            if (File::exists($source)) {
                File::copy($source, $outputDir.'/'.$file);
            }
        }
    }
}
