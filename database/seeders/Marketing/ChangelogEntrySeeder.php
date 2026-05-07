<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\ChangelogEntry;
use Illuminate\Database\Seeder;

class ChangelogEntrySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'title'        => 'HR Module: Leave Management Overhaul',
                'type'         => 'feature',
                'body'         => "We have completely rebuilt the leave management section of the HR module. New capabilities include: custom leave types with their own accrual rules, a company-wide leave calendar, manager delegation for approval workflows, and GDPR-compliant data retention settings. The new leave calendar gives HR teams full visibility of who is out across the organisation at any point in time.",
                'screenshot'   => null,
                'docs_url'     => null,
                'is_published' => true,
                'published_at' => '2025-03-15',
            ],
            [
                'title'        => 'Projects Module: Time Tracking & Timesheets',
                'type'         => 'feature',
                'body'         => "Time tracking is now available in the Projects module. Employees can log time against tasks using a running timer or manual entry. At the end of each week, FlowFlex automatically compiles a timesheet per employee for manager approval. Approved timesheets integrate with the HR module for payroll calculations.",
                'screenshot'   => null,
                'docs_url'     => null,
                'is_published' => true,
                'published_at' => '2025-07-01',
            ],
            [
                'title'        => 'Performance Improvement: Dashboard Load Time Reduced by 40%',
                'type'         => 'improvement',
                'body'         => "We identified and resolved several N+1 query patterns affecting the main workspace dashboard. By adding targeted eager loading and Redis caching for permission and module registry data, the average dashboard load time has dropped from 1.2 seconds to under 0.7 seconds. This improvement affects all users across all modules.",
                'screenshot'   => null,
                'docs_url'     => null,
                'is_published' => true,
                'published_at' => '2025-10-22',
            ],
            [
                'title'        => 'Fix: Incorrect Vacation Day Accrual for Part-Time Employees',
                'type'         => 'fix',
                'body'         => "We resolved a bug where part-time employees (working fewer than 5 days per week) were accruing vacation days at the full-time rate instead of proportionally. Affected companies have been notified and their balances have been corrected automatically. No action is required on your end.",
                'screenshot'   => null,
                'docs_url'     => null,
                'is_published' => true,
                'published_at' => '2026-01-10',
            ],
            [
                'title'        => 'API: New Endpoints for Employee Records and Leave Requests',
                'type'         => 'feature',
                'body'         => "The FlowFlex REST API now exposes endpoints for employee records (list, create, update, archive) and leave requests (list, approve, decline). These endpoints are available to all workspaces with a valid API key. Full documentation is available at api.flowflex.io/docs. Rate limits apply: 1,000 requests per minute per key.",
                'screenshot'   => null,
                'docs_url'     => 'https://api.flowflex.io/docs',
                'is_published' => true,
                'published_at' => '2026-03-05',
            ],
        ];

        foreach ($entries as $data) {
            ChangelogEntry::firstOrCreate(['title' => $data['title']], $data);
        }
    }
}
