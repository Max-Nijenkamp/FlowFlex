<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'          => 'Product Updates',
                'slug'          => 'product-updates',
                'description'   => 'Latest features, releases, and improvements to the FlowFlex platform.',
                'display_order' => 1,
                'is_published'  => true,
            ],
            [
                'name'          => 'Industry Insights',
                'slug'          => 'industry-insights',
                'description'   => 'Trends, research, and analysis from the world of HR and workforce management.',
                'display_order' => 2,
                'is_published'  => true,
            ],
            [
                'name'          => 'How-To Guides',
                'slug'          => 'how-to-guides',
                'description'   => 'Step-by-step tutorials for getting the most out of FlowFlex.',
                'display_order' => 3,
                'is_published'  => true,
            ],
            [
                'name'          => 'Customer Stories',
                'slug'          => 'customer-stories',
                'description'   => 'Real-world results from businesses using FlowFlex to transform their HR.',
                'display_order' => 4,
                'is_published'  => true,
            ],
        ];

        foreach ($categories as $data) {
            BlogCategory::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
