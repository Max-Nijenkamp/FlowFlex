<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name'          => 'Marieke van den Berg',
                'role'          => 'HR Manager',
                'company'       => 'Logistics Plus BV',
                'quote'         => 'FlowFlex has completely transformed how we manage our 80-person team. Leave requests that used to take days to process are now handled in minutes. The payroll integration alone has saved us countless hours every month.',
                'photo'         => null,
                'is_featured'   => true,
                'display_order' => 1,
                'is_published'  => true,
            ],
            [
                'name'          => 'James Whitfield',
                'role'          => 'Head of People',
                'company'       => 'TechSpark Ltd',
                'quote'         => 'We evaluated five HR platforms before choosing FlowFlex. The modular approach was the deciding factor — we only pay for what we actually use. The onboarding module in particular has made a huge difference to how our new hires experience their first week.',
                'photo'         => null,
                'is_featured'   => true,
                'display_order' => 2,
                'is_published'  => true,
            ],
            [
                'name'          => 'Sandra Dekker',
                'role'          => 'Operations Director',
                'company'       => 'Bouw & Infra Groep',
                'quote'         => 'I was worried FlowFlex would be too technical for our team, but the interface is genuinely intuitive. Our supervisors picked it up without any training. Support has been excellent whenever we have had questions.',
                'photo'         => null,
                'is_featured'   => false,
                'display_order' => 3,
                'is_published'  => true,
            ],
            [
                'name'          => 'Robert Chambers',
                'role'          => 'Finance & HR Director',
                'company'       => 'Chambers Consulting Group',
                'quote'         => 'The combination of HR, projects, and time tracking in a single platform has given us visibility we never had before. We can now see exactly where our team is spending time and align resources accordingly.',
                'photo'         => null,
                'is_featured'   => false,
                'display_order' => 4,
                'is_published'  => true,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::firstOrCreate(
                ['name' => $data['name'], 'company' => $data['company']],
                $data
            );
        }
    }
}
