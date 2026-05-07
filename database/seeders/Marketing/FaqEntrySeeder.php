<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\FaqEntry;
use Illuminate\Database\Seeder;

class FaqEntrySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // General (4 entries)
            [
                'question'      => 'What is FlowFlex?',
                'answer'        => 'FlowFlex is a modular, multi-tenant SaaS platform for small and medium-sized businesses. It combines HR, project management, time tracking, and more in a single workspace. You activate only the modules you need and pay per module.',
                'context'       => 'general',
                'display_order' => 1,
                'is_published'  => true,
            ],
            [
                'question'      => 'Which modules are available?',
                'answer'        => 'FlowFlex currently offers HR (employee records, leave management, onboarding, payroll), Projects (tasks, time tracking, timesheets), and Documents. New modules are released regularly. You can see the full module list and enable them from your Workspace Settings.',
                'context'       => 'general',
                'display_order' => 2,
                'is_published'  => true,
            ],
            [
                'question'      => 'Is FlowFlex suitable for companies outside the Netherlands?',
                'answer'        => 'FlowFlex is designed primarily for Dutch and EU-based businesses. The HR module handles Dutch employment law and GDPR compliance out of the box. International support for additional jurisdictions is on our roadmap.',
                'context'       => 'general',
                'display_order' => 3,
                'is_published'  => true,
            ],
            [
                'question'      => 'How do I get started?',
                'answer'        => 'There is no self-registration for FlowFlex — accounts are created by our team to ensure a proper onboarding experience. Book a demo and we will set up your workspace, migrate any existing data, and walk you through the platform in a live session.',
                'context'       => 'general',
                'display_order' => 4,
                'is_published'  => true,
            ],
            // Pricing (2 entries)
            [
                'question'      => 'How does FlowFlex pricing work?',
                'answer'        => 'FlowFlex uses a per-module, per-seat pricing model. You pay a monthly fee for each module you activate, multiplied by the number of active employees in your workspace. Unused modules are never charged. Volume discounts apply for teams over 100 people.',
                'context'       => 'pricing',
                'display_order' => 1,
                'is_published'  => true,
            ],
            [
                'question'      => 'Is there a free trial?',
                'answer'        => 'Yes. Every new module activation includes a 30-day free trial. No credit card is required to start a trial. At the end of the trial period, you can choose to subscribe or disable the module — there is no automatic charge.',
                'context'       => 'pricing',
                'display_order' => 2,
                'is_published'  => true,
            ],
            // Security (2 entries)
            [
                'question'      => 'How does FlowFlex protect my data?',
                'answer'        => 'All data is encrypted at rest using AES-256 and in transit using TLS 1.3. Sensitive fields such as salary records, bank details, and API keys receive an additional layer of application-level encryption. FlowFlex is hosted on AWS infrastructure in the EU (Frankfurt region).',
                'context'       => 'security',
                'display_order' => 1,
                'is_published'  => true,
            ],
            [
                'question'      => 'Is FlowFlex GDPR compliant?',
                'answer'        => 'Yes. FlowFlex is designed for GDPR compliance from the ground up. Data is stored in the EU, we offer a Data Processing Agreement (DPA) for all customers, and every record modification is logged in a tamper-evident audit trail. Employees have the right to access and request deletion of their personal data via the workspace settings.',
                'context'       => 'security',
                'display_order' => 2,
                'is_published'  => true,
            ],
        ];

        foreach ($entries as $data) {
            FaqEntry::firstOrCreate(
                ['question' => $data['question'], 'context' => $data['context']],
                $data
            );
        }
    }
}
