<?php

use App\Models\Winner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->string('company_website')->nullable()->after('company_name');
            $table->string('verification_slug')->nullable()->unique()->after('company_website');
        });

        foreach (Winner::with('category')->whereNull('verification_slug')->get() as $winner) {
            $winner->verification_slug = Winner::makeVerificationSlug(
                $winner->company_name,
                $winner->category?->title ?? '',
                $winner->won_at?->year
            );

            $winner->save();
        }
    }

    public function down(): void
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->dropUnique(['verification_slug']);
            $table->dropColumn(['company_website', 'verification_slug']);
        });
    }
};
