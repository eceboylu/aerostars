<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $settings = [
        'site_logo' => '/uploads/flyoneers-logo-2027.png',
        'hero_emblem' => '/uploads/flyoneers-emblem.png',
    ];

    public function up(): void
    {
        foreach ($this->settings as $key => $value) {
            if (! DB::table('site_settings')->where('key', $key)->exists()) {
                DB::table('site_settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', array_keys($this->settings))->delete();
    }
};
