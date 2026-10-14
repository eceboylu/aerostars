<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_page_id',
        'company_name',
        'verification_slug',
        'logo',
        'description',
        'won_at',
        'status',
    ];

    protected $casts = [
        'won_at' => 'date',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Page::class, 'category_page_id');
    }

    public static function makeVerificationSlug(string $companyName, string $categoryTitle, ?int $year): string
    {
        $slug = Str::slug(str_replace('.', ' ', $companyName.' '.$categoryTitle.' '.$year)) ?: 'winner';
        $uniqueSlug = $slug;
        $attempt = 1;

        while (self::where('verification_slug', $uniqueSlug)->exists()) {
            $attempt++;
            $uniqueSlug = $slug.'-'.$attempt;
        }

        return $uniqueSlug;
    }
}
