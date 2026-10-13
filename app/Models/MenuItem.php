<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
