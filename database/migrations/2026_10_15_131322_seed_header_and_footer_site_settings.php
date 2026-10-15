<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The header button and footer texts as they were in the layout before
     * they became editable in the admin. Existing rows are never overwritten.
     *
     * @var array<string, string>
     */
    private array $settings = [
        'header_cta_label' => 'Enter the awards',
        'header_cta_url' => '/enter/',
        'footer_description' => 'International awards for the companies that sell, provide and manage airport passenger services.',
        'footer_copyright' => '© 2027 Flyoneers. All rights reserved.',
        'footer_email' => 'awards@flyoneers.example',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        foreach ($this->settings as $key => $value) {
            if (! DB::table('site_settings')->where('key', $key)->exists()) {
                DB::table('site_settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', array_keys($this->settings))->delete();
    }
};
