<?php

namespace App\Models;

use App\Services\Cms\HtmlSanitizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class MenuItem extends Model
{
    protected $fillable = [
        'location',
        'parent_id',
        'page_id',
        'link_type',
        'label',
        'custom_url',
        'sort_order',
        'open_in_new_tab',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
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

    public static function validationRules(string $location, array $forbiddenParentIds = []): array
    {
        return [
            'parent_id' => [
                'nullable',
                Rule::exists('menu_items', 'id')->where('location', $location),
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
        ];
    }

    public function url(): ?string
    {
        if ($this->link_type === 'page') {
            return $this->page ? route('site.page', $this->page->slug) : null;
        }

        if ($this->link_type === 'custom') {
            return $this->custom_url;
        }

        return null;
    }

    public function isVisible(): bool
    {
        if (! $this->status) {
            return false;
        }

        if ($this->link_type === 'page') {
            return $this->page !== null && $this->page->status;
        }

        return true;
    }
}
