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
        'category_id',
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
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

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

    public function seoOgTitle(): string
    {
        return $this->seoTitle();
    }

    public function seoOgDescription(): ?string
    {
        return $this->seoDescription();
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
