<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\DemoRequest;
use Illuminate\Database\Seeder;

class DemoRequestSeeder extends Seeder
{
    public function run(): void
    {
        $requests = [
            [
                'first_name'         => 'Ingrid',
                'last_name'          => 'van der Laan',
                'email'              => 'ingrid@logistics-plus.nl',
                'company_name'       => 'Logistics Plus BV',
                'company_size'       => '51-200',
                'modules_interested' => ['hr', 'projects'],
                'heard_from'         => 'LinkedIn',
                'notes'              => 'Looking to replace Excel-based HR. Interested in leave management and onboarding.',
                'ip_address'         => '85.145.12.34',
                'user_agent'         => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'utm_source'         => 'linkedin',
                'utm_medium'         => 'social',
                'utm_campaign'       => 'q1-awareness',
                'status'             => 'new',
            ],
            [
                'first_name'         => 'Robert',
                'last_name'          => 'Chambers',
                'email'              => 'robert@chambers-consulting.co.uk',
                'company_name'       => 'Chambers Consulting Group',
                'company_size'       => '11-50',
                'modules_interested' => ['hr', 'projects', 'documents'],
                'heard_from'         => 'Google Search',
                'notes'              => 'Currently using a competitor. Contract up for renewal in 3 months.',
                'ip_address'         => '92.68.44.21',
                'user_agent'         => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'utm_source'         => 'google',
                'utm_medium'         => 'cpc',
                'utm_campaign'       => 'brand-uk',
                'status'             => 'contacted',
            ],
            [
                'first_name'         => 'Sandra',
                'last_name'          => 'Dekker',
                'email'              => 'sandra@bouw-infra.nl',
                'company_name'       => 'Bouw & Infra Groep',
                'company_size'       => '201-500',
                'modules_interested' => ['hr'],
                'heard_from'         => 'Referral — Logistics Plus',
                'notes'              => 'Needs Dutch payroll compliance. Large team across 5 locations.',
                'ip_address'         => '84.25.110.5',
                'user_agent'         => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'utm_source'         => null,
                'utm_medium'         => null,
                'utm_campaign'       => null,
                'status'             => 'demo_scheduled',
                'scheduled_at'       => now()->addDays(3),
            ],
            [
                'first_name'         => 'Emma',
                'last_name'          => 'Hartman',
                'email'              => 'emma@techspark.io',
                'company_name'       => 'TechSpark Ltd',
                'company_size'       => '11-50',
                'modules_interested' => ['hr', 'projects'],
                'heard_from'         => 'Product Hunt',
                'notes'              => null,
                'ip_address'         => '77.166.89.200',
                'user_agent'         => 'Mozilla/5.0 (X11; Linux x86_64)',
                'utm_source'         => 'product-hunt',
                'utm_medium'         => 'referral',
                'utm_campaign'       => null,
                'status'             => 'converted',
            ],
            [
                'first_name'         => 'Henk',
                'last_name'          => 'Mulder',
                'email'              => 'henk@mulder-transport.nl',
                'company_name'       => 'Mulder Transport',
                'company_size'       => '1-10',
                'modules_interested' => ['hr'],
                'heard_from'         => 'Facebook Ad',
                'notes'              => 'Small team, tight budget. Went with a cheaper option.',
                'ip_address'         => '83.87.45.19',
                'user_agent'         => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0)',
                'utm_source'         => 'facebook',
                'utm_medium'         => 'paid-social',
                'utm_campaign'       => 'sme-retarget',
                'status'             => 'lost',
            ],
        ];

        foreach ($requests as $data) {
            DemoRequest::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
