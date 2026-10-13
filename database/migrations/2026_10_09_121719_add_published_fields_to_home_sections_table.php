<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->string('published_mode')->nullable()->after('settings');
            $table->longText('published_custom_html')->nullable()->after('published_mode');
            $table->longText('published_custom_css')->nullable()->after('published_custom_html');
            $table->json('published_settings')->nullable()->after('published_custom_css');
        });

        DB::table('home_sections')->update([
            'published_mode' => DB::raw('mode'),
            'published_custom_html' => DB::raw('custom_html'),
            'published_custom_css' => DB::raw('custom_css'),
            'published_settings' => DB::raw('settings'),
        ]);
    }

    public function down(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            $table->dropColumn(['published_mode', 'published_custom_html', 'published_custom_css', 'published_settings']);
        });
    }
};
