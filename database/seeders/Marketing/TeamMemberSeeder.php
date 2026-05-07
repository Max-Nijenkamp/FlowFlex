<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name'          => 'Max van der Hoeven',
                'role'          => 'CEO & Co-founder',
                'bio'           => 'Max spent 10 years building HR systems at enterprise companies before founding FlowFlex to bring modern workforce tools to SMEs. He is passionate about making complex software genuinely simple.',
                'photo'         => null,
                'linkedin_url'  => null,
                'twitter_url'   => null,
                'display_order' => 1,
                'is_published'  => true,
            ],
            [
                'name'          => 'Lotte Visser',
                'role'          => 'CTO & Co-founder',
                'bio'           => 'Lotte leads engineering at FlowFlex, having previously built distributed systems at a major Dutch fintech. She cares deeply about reliability, security, and developer experience.',
                'photo'         => null,
                'linkedin_url'  => null,
                'twitter_url'   => null,
                'display_order' => 2,
                'is_published'  => true,
            ],
            [
                'name'          => 'Daan Brouwer',
                'role'          => 'Head of Customer Success',
                'bio'           => 'Daan has helped over 200 companies adopt new HR software during his career and now leads customer success at FlowFlex. He ensures every customer gets maximum value from the platform.',
                'photo'         => null,
                'linkedin_url'  => null,
                'twitter_url'   => null,
                'display_order' => 3,
                'is_published'  => true,
            ],
        ];

        foreach ($members as $data) {
            TeamMember::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
