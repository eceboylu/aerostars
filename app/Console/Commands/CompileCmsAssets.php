<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

#[Signature('pages:compile-cms-assets')]
#[Description('Dump CMS page HTML and recompile the scoped Tailwind utilities used inside it (#cms-page-content).')]
class CompileCmsAssets extends Command
{
    private const SOURCE_PATH = 'cms/tailwind-source.html';

    public function handle(): int
    {
        $html = Page::query()
            ->whereNotNull('custom_html')
            ->pluck('custom_html')
            ->implode("\n");

        File::ensureDirectoryExists(storage_path('app/cms'));
        File::put(storage_path('app/'.self::SOURCE_PATH), $html);

        $result = Process::path(base_path())
            ->timeout(60)
            ->run(['node', 'build-cms-tailwind.cjs']);

        if ($result->failed()) {
            $this->error('Tailwind CMS compile failed: '.$result->errorOutput());

            return self::FAILURE;
        }

        $this->info(trim($result->output()));

        return self::SUCCESS;
    }
}
