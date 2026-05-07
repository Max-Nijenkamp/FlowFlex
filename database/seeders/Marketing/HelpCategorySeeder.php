<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\HelpCategory;
use Illuminate\Database\Seeder;

class HelpCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'          => 'Getting Started',
                'slug'          => 'getting-started',
                'description'   => 'Everything you need to set up your FlowFlex workspace for the first time.',
                'icon'          => 'rocket',
                'parent_id'     => null,
                'display_order' => 1,
                'is_published'  => true,
            ],
            [
                'name'          => 'HR Module',
                'slug'          => 'hr-module',
                'description'   => 'Guides for managing employees, leave requests, departments, and payroll.',
                'icon'          => 'users',
                'parent_id'     => null,
                'display_order' => 2,
                'is_published'  => true,
            ],
            [
                'name'          => 'Billing & Plans',
                'slug'          => 'billing-plans',
                'description'   => 'How module billing works, how to upgrade your plan, and how to manage invoices.',
                'icon'          => 'credit-card',
                'parent_id'     => null,
                'display_order' => 3,
                'is_published'  => true,
            ],
        ];

        foreach ($categories as $data) {
            HelpCategory::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
