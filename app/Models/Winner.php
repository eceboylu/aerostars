<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $fillable = [
        'category_page_id',
        'company_name',
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
}
