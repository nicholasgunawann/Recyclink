<?php

namespace Database\Seeders;

use App\Models\WasteCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WasteCategorySeeder extends Seeder
{
    // ponytail: data array — no abstraction needed
    private array $categories = [
        ['category_name' => 'Plastik',                                                   'icon' => 'icon-plastic',   'color' => '#3B82F6'],
        ['category_name' => 'Logam',                                                     'icon' => 'icon-metal',     'color' => '#6B7280'],
        ['category_name' => 'Tekstil',                                                   'icon' => 'icon-textile',   'color' => '#EC4899'],
        ['category_name' => 'Kertas (Kardus, kertas)',                                   'icon' => 'icon-paper',     'color' => '#F59E0B'],
        ['category_name' => 'Kimia',                                                     'icon' => 'icon-chemical',  'color' => '#EF4444'],
        ['category_name' => 'Elektronik',                                                'icon' => 'icon-electronic','color' => '#8B5CF6'],
        ['category_name' => 'Cairan/Minyak',                                             'icon' => 'icon-oil',       'color' => '#D97706'],
        ['category_name' => 'Limbah organik/sisa makanan (rumput, daun, sisa kulit makanan, dll.)', 'icon' => 'icon-organic','color' => '#16A34A'],
        ['category_name' => 'Lainnya (Kayu, keramik, kaca dll)',                         'icon' => 'icon-other',     'color' => '#64748B'],
    ];

    public function run(): void
    {
        $sortOrder = 1;
        foreach ($this->categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = WasteCategory::updateOrCreate(
                ['slug' => Str::slug($categoryData['category_name'])],
                array_merge($categoryData, ['parent_id' => null, 'is_active' => true, 'sort_order' => $sortOrder++])
            );

            $childOrder = 1;
            foreach ($children as $child) {
                WasteCategory::updateOrCreate(
                    ['slug' => Str::slug($child['category_name'])],
                    array_merge($child, ['parent_id' => $parent->id, 'is_active' => true, 'sort_order' => $childOrder++])
                );
            }

            $this->command->info("✓ {$parent->category_name} (" . count($children) . ' sub)');
        }
    }
}
