<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public const HOMEPAGE_DEFAULTS = [
        'hero_eyebrow' => 'Airport passenger services awards · 2027',
        'hero_title' => 'Awards for the companies behind the airport journey',
        'hero_lede' => 'Flyoneers recognises the platforms, providers and technology that sell, book and manage airport passenger services around the world.',
        'stat_1_num' => '15',
        'stat_1_label' => 'Award categories',
        'stat_2_num' => '4',
        'stat_2_label' => 'Award families',
        'stat_3_num' => '10',
        'stat_3_label' => 'Judging criteria',
        'stat_4_num' => '1',
        'stat_4_label' => 'Winner per category',
        'closing_title' => 'Entries open on 1 February 2027',
        'closing_text' => 'Choose your categories, send your evidence and let the jury decide.',
        'closing_button_1_text' => 'Start your entry',
        'closing_button_2_text' => 'See how it works',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function getMany(array $keys): array
    {
        $values = static::query()->whereIn('key', $keys)->pluck('value', 'key');

        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $values[$key] ?? null;
        }

        return $result;
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
