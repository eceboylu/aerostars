<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $order = 0;

        foreach (HomeSection::DEFAULT_KEYS as $key => $label) {
            HomeSection::firstOrCreate(
                ['key' => $key],
                [
                    'label' => $label,
                    'mode' => 'default',
                    'status' => true,
                    'sort_order' => $order,
                ]
            );

            $order++;
        }
    }
}
