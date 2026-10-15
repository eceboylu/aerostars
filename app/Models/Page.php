<?php

namespace App\Models;

use App\Services\Cms\CssScoper;
use App\Services\Cms\HtmlSanitizer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'custom_html',
        'custom_css',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots_index',
        'badge_image',
        'icon',
    ];

    protected function customHtml(): Attribute
    {
        return Attribute::set(
            fn (?string $value): ?string => app(HtmlSanitizer::class)->stripEventHandlers($value)
        );
    }

    protected function scopedCustomCss(): Attribute
    {
        return Attribute::get(
            fn (): ?string => app(CssScoper::class)->scope($this->custom_css)
        );
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function descendantIds(): array
    {
        $ids = [];

        foreach ($this->children as $child) {
            $ids[] = $child->id;

            $ids = array_merge(
                $ids,
                $child->descendantIds()
            );
        }

        return $ids;
    }

    public function activeChildren()
    {
        return $this->children()
            ->where('status', true)
            ->orderBy('title');
    }

    public static function categoriesRoot(): ?Page
    {
        return static::where('slug', 'categories')->first();
    }

    public static function activeAwardCategoryCount(): int
    {
        $categoriesRoot = static::categoriesRoot();

        if (! $categoriesRoot) {
            return 0;
        }

        return static::where('status', true)
            ->whereIn('parent_id', $categoriesRoot->children()->where('status', true)->select('id'))
            ->count();
    }

    public function publicUrl(): string
    {
        return route('site.page', $this->slug);
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function seoDescription(): ?string
    {
        return $this->meta_description;
    }

    public function shortDescription(): string
    {
        $description = (string) $this->meta_description;
        $prefix = $this->title.' at the Flyoneers Awards 2027. ';

        if (str_starts_with($description, $prefix)) {
            return substr($description, strlen($prefix));
        }

        return $description;
    }

    public function seoCanonicalUrl(): string
    {
        return $this->canonical_url ?: $this->publicUrl();
    }

    public function seoRobotsContent(): string
    {
        return $this->robots_index ? 'index,follow' : 'noindex,follow';
    }

    public function extractFaqPreview(int $limit = 3): array
    {
        if (! $this->custom_html) {
            return [];
        }

        $dom = new \DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8"?><!DOCTYPE html><html><body>'.$this->custom_html.'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_use_internal_errors($previous);

        $items = [];

        foreach ($dom->getElementsByTagName('details') as $details) {
            if (count($items) >= $limit) {
                break;
            }

            $question = null;
            $answer = null;

            foreach ($details->childNodes as $child) {
                if ($child->nodeName === 'summary' && $question === null) {
                    $question = trim($child->textContent);
                } elseif ($child->nodeName === 'p' && $answer === null) {
                    $answer = trim($child->textContent);
                }
            }

            if ($question !== null) {
                $items[] = ['question' => $question, 'answer' => $answer ?? ''];
            }
        }

        return $items;
    }
}
