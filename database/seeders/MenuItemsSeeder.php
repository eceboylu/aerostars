<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuItemsSeeder extends Seeder
{
    private const HEADER_ITEMS = [
        ['label' => 'Categories', 'page' => 'categories'],
        ['label' => 'How it works', 'page' => 'how-it-works'],
        ['label' => 'Judging', 'page' => 'judging'],
        ['label' => 'Winners', 'page' => 'winners'],
        ['label' => 'About', 'page' => 'about'],
    ];

    private const FOOTER_GROUPS = [
        [
            'label' => 'Awards',
            'children' => [
                ['label' => 'Airport Lounge', 'page' => 'categories/airport-lounge'],
                ['label' => 'Fast Track', 'page' => 'categories/fast-track'],
                ['label' => 'Meet & Greet', 'page' => 'categories/meet-and-greet'],
                ['label' => 'Airport Service Platforms', 'page' => 'categories/airport-service-platforms'],
                ['label' => 'Airport Services Technology', 'page' => 'categories/airport-services-technology'],
            ],
        ],
        [
            'label' => 'Programme',
            'children' => [
                ['label' => 'How it works', 'page' => 'how-it-works'],
                ['label' => 'Judging', 'page' => 'judging'],
                ['label' => 'Winners', 'page' => 'winners'],
                ['label' => 'Questions', 'page' => 'faq'],
                ['label' => 'Enter the awards', 'page' => 'enter'],
            ],
        ],
        [
            'label' => 'AirStars',
            'children' => [
                ['label' => 'About', 'page' => 'about'],
                ['label' => 'Contact', 'page' => 'contact'],
                ['label' => 'Terms', 'page' => 'terms'],
                ['label' => 'Privacy', 'page' => 'privacy'],
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::HEADER_ITEMS as $index => $item) {
            $this->updateOrCreateItem('header', null, $index, $item['label'], $item['page']);
        }

        foreach (self::FOOTER_GROUPS as $groupIndex => $group) {
            $groupItem = $this->updateOrCreateItem('footer', null, $groupIndex, $group['label'], null);

            foreach ($group['children'] as $childIndex => $child) {
                $this->updateOrCreateItem('footer', $groupItem->id, $childIndex, $child['label'], $child['page']);
            }
        }
    }

    private function updateOrCreateItem(string $location, ?int $parentId, int $sortOrder, string $label, ?string $pageSlug): MenuItem
    {
        $page = $pageSlug ? Page::where('slug', $pageSlug)->first() : null;

        return MenuItem::updateOrCreate(
            [
                'location' => $location,
                'parent_id' => $parentId,
                'label' => $label,
            ],
            [
                'page_id' => $page?->id,
                'link_type' => $page ? 'page' : 'none',
                'custom_url' => null,
                'sort_order' => $sortOrder,
                'open_in_new_tab' => false,
                'status' => true,
            ]
        );
    }
}
