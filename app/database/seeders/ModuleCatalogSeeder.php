<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ModuleCatalog;
use Illuminate\Database\Seeder;

class ModuleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $modules = $this->getModules();

        foreach ($modules as $module) {
            ModuleCatalog::updateOrCreate(
                ['module_key' => $module['module_key']],
                $module,
            );
        }
    }

    private function getModules(): array
    {
        return [
            // ── Core (free) ───────────────────────────────────────────────
            ['module_key' => 'core.auth',          'domain' => 'core',    'name' => 'Authentication & Identity',  'per_user_monthly_price' => 0.00, 'is_active' => true],
            ['module_key' => 'core.notifications',  'domain' => 'core',    'name' => 'Notifications & Alerts',     'per_user_monthly_price' => 0.00, 'is_active' => true],
            ['module_key' => 'core.audit-log',      'domain' => 'core',    'name' => 'Audit Log',                  'per_user_monthly_price' => 0.00, 'is_active' => true],
            ['module_key' => 'core.file-storage',   'domain' => 'core',    'name' => 'File Storage',               'per_user_monthly_price' => 0.00, 'is_active' => true],
            ['module_key' => 'core.rbac',           'domain' => 'core',    'name' => 'Roles & Permissions',        'per_user_monthly_price' => 0.00, 'is_active' => true],

            // ── HR & People ───────────────────────────────────────────────
            ['module_key' => 'hr.profiles',         'domain' => 'hr',      'name' => 'Employee Profiles',          'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'hr.leave',            'domain' => 'hr',      'name' => 'Leave Management',           'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'hr.onboarding',       'domain' => 'hr',      'name' => 'Onboarding',                 'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'hr.offboarding',      'domain' => 'hr',      'name' => 'Offboarding',                'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'hr.payroll',          'domain' => 'hr',      'name' => 'Payroll',                    'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'hr.performance',      'domain' => 'hr',      'name' => 'Performance Reviews',        'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'hr.recruitment',      'domain' => 'hr',      'name' => 'Recruitment & ATS',          'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'hr.timetracking',     'domain' => 'hr',      'name' => 'Time Tracking',              'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'hr.expenses',         'domain' => 'hr',      'name' => 'Employee Expenses',          'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'hr.documents',        'domain' => 'hr',      'name' => 'HR Documents',               'per_user_monthly_price' => 0.75, 'is_active' => false],
            ['module_key' => 'hr.benefits',         'domain' => 'hr',      'name' => 'Benefits Administration',    'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Projects & Work ───────────────────────────────────────────
            ['module_key' => 'projects.tasks',      'domain' => 'projects', 'name' => 'Tasks & To-Dos',            'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'projects.kanban',     'domain' => 'projects', 'name' => 'Kanban Boards',             'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'projects.gantt',      'domain' => 'projects', 'name' => 'Gantt Charts',              'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'projects.sprints',    'domain' => 'projects', 'name' => 'Sprints & Agile',           'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'projects.resources',  'domain' => 'projects', 'name' => 'Resource Planning',         'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'projects.milestones', 'domain' => 'projects', 'name' => 'Milestones & Goals',        'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Finance & Accounting ──────────────────────────────────────
            ['module_key' => 'finance.invoicing',   'domain' => 'finance',  'name' => 'Invoicing',                 'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'finance.expenses',    'domain' => 'finance',  'name' => 'Expense Management',        'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'finance.budgeting',   'domain' => 'finance',  'name' => 'Budgeting',                 'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'finance.payroll',     'domain' => 'finance',  'name' => 'Payroll Processing',        'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'finance.reporting',   'domain' => 'finance',  'name' => 'Financial Reporting',       'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'finance.accounts',    'domain' => 'finance',  'name' => 'Chart of Accounts',         'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'finance.vat',         'domain' => 'finance',  'name' => 'VAT & Tax Management',      'per_user_monthly_price' => 1.50, 'is_active' => false],

            // ── CRM & Sales ───────────────────────────────────────────────
            ['module_key' => 'crm.contacts',        'domain' => 'crm',     'name' => 'Contacts',                   'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'crm.pipeline',        'domain' => 'crm',     'name' => 'Sales Pipeline',             'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'crm.leads',           'domain' => 'crm',     'name' => 'Lead Management',            'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'crm.quotes',          'domain' => 'crm',     'name' => 'Quotes & Proposals',         'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'crm.accounts',        'domain' => 'crm',     'name' => 'Account Management',         'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Marketing & Content ───────────────────────────────────────
            ['module_key' => 'marketing.email',     'domain' => 'marketing', 'name' => 'Email Marketing',          'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'marketing.cms',       'domain' => 'marketing', 'name' => 'Content Management',       'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'marketing.seo',       'domain' => 'marketing', 'name' => 'SEO Tools',                'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'marketing.forms',     'domain' => 'marketing', 'name' => 'Forms & Landing Pages',    'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'marketing.social',    'domain' => 'marketing', 'name' => 'Social Media Scheduler',   'per_user_monthly_price' => 1.50, 'is_active' => false],

            // ── Operations ────────────────────────────────────────────────
            ['module_key' => 'ops.inventory',       'domain' => 'operations', 'name' => 'Inventory Management',   'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'ops.purchasing',      'domain' => 'operations', 'name' => 'Purchase Orders',        'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'ops.warehousing',     'domain' => 'operations', 'name' => 'Warehouse Management',   'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'ops.logistics',       'domain' => 'operations', 'name' => 'Logistics & Shipping',   'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── Analytics & BI ────────────────────────────────────────────
            ['module_key' => 'analytics.reports',   'domain' => 'analytics', 'name' => 'Reports & Dashboards',   'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'analytics.kpi',       'domain' => 'analytics', 'name' => 'KPI Tracking',           'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'analytics.bi',        'domain' => 'analytics', 'name' => 'Business Intelligence',  'per_user_monthly_price' => 3.00, 'is_active' => false],

            // ── IT & Security ──────────────────────────────────────────────
            ['module_key' => 'it.assets',           'domain' => 'it',      'name' => 'Asset Management',          'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'it.helpdesk',         'domain' => 'it',      'name' => 'Help Desk & Ticketing',     'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'it.sso',              'domain' => 'it',      'name' => 'SSO & Identity',            'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'it.2fa',              'domain' => 'it',      'name' => '2FA Enforcement',           'per_user_monthly_price' => 0.50, 'is_active' => false],

            // ── Legal & Compliance ─────────────────────────────────────────
            ['module_key' => 'legal.contracts',     'domain' => 'legal',   'name' => 'Contract Management',       'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'legal.gdpr',          'domain' => 'legal',   'name' => 'GDPR & Privacy Tools',      'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'legal.policies',      'domain' => 'legal',   'name' => 'Policy Management',         'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── E-commerce ────────────────────────────────────────────────
            ['module_key' => 'ecommerce.shop',      'domain' => 'ecommerce', 'name' => 'Online Store',            'per_user_monthly_price' => 3.00, 'is_active' => false],
            ['module_key' => 'ecommerce.orders',    'domain' => 'ecommerce', 'name' => 'Order Management',        'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'ecommerce.catalog',   'domain' => 'ecommerce', 'name' => 'Product Catalog',         'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'ecommerce.payments',  'domain' => 'ecommerce', 'name' => 'Payment Processing',      'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── Communications ────────────────────────────────────────────
            ['module_key' => 'comms.chat',          'domain' => 'communications', 'name' => 'Team Chat',          'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'comms.video',         'domain' => 'communications', 'name' => 'Video Meetings',     'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'comms.announcements', 'domain' => 'communications', 'name' => 'Announcements',      'per_user_monthly_price' => 0.50, 'is_active' => false],

            // ── Learning & Development ─────────────────────────────────────
            ['module_key' => 'lms.courses',         'domain' => 'learning', 'name' => 'Online Courses',           'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'lms.skills',          'domain' => 'learning', 'name' => 'Skills & Competencies',    'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'lms.certifications',  'domain' => 'learning', 'name' => 'Certifications',           'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── AI & Automation ────────────────────────────────────────────
            ['module_key' => 'ai.copilot',          'domain' => 'ai',      'name' => 'AI Copilot',                'per_user_monthly_price' => 3.00, 'is_active' => false],
            ['module_key' => 'ai.workflows',        'domain' => 'ai',      'name' => 'AI Workflow Automation',    'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'ai.analytics',        'domain' => 'ai',      'name' => 'Predictive Analytics',      'per_user_monthly_price' => 3.00, 'is_active' => false],

            // ── Community & Social ─────────────────────────────────────────
            ['module_key' => 'community.forum',     'domain' => 'community', 'name' => 'Community Forum',         'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'community.events',    'domain' => 'community', 'name' => 'Community Events',        'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Document Management ────────────────────────────────────────
            ['module_key' => 'docs.dms',            'domain' => 'documents', 'name' => 'Document Management',     'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'docs.esign',          'domain' => 'documents', 'name' => 'E-Signatures',            'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'docs.templates',      'domain' => 'documents', 'name' => 'Document Templates',      'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Customer Success ───────────────────────────────────────────
            ['module_key' => 'cs.onboarding',       'domain' => 'customer-success', 'name' => 'Customer Onboarding',  'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'cs.health',           'domain' => 'customer-success', 'name' => 'Customer Health Score', 'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'cs.feedback',         'domain' => 'customer-success', 'name' => 'NPS & Feedback',        'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Subscription Billing & RevOps ──────────────────────────────
            ['module_key' => 'billing.subscriptions', 'domain' => 'billing', 'name' => 'Subscription Management', 'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'billing.revenue',     'domain' => 'billing', 'name' => 'Revenue Operations',        'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── Procurement ────────────────────────────────────────────────
            ['module_key' => 'procurement.requests', 'domain' => 'procurement', 'name' => 'Purchase Requests',   'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'procurement.vendors', 'domain' => 'procurement', 'name' => 'Vendor Management',    'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'procurement.contracts', 'domain' => 'procurement', 'name' => 'Supplier Contracts', 'per_user_monthly_price' => 1.50, 'is_active' => false],

            // ── Events Management ──────────────────────────────────────────
            ['module_key' => 'events.planning',     'domain' => 'events', 'name' => 'Event Planning',             'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'events.tickets',      'domain' => 'events', 'name' => 'Ticketing & Registration',   'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── Whistleblowing ─────────────────────────────────────────────
            ['module_key' => 'ethics.whistleblow',  'domain' => 'ethics', 'name' => 'Whistleblowing Portal',      'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Field Service ──────────────────────────────────────────────
            ['module_key' => 'field.work-orders',   'domain' => 'field-service', 'name' => 'Work Orders',         'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'field.scheduling',    'domain' => 'field-service', 'name' => 'Field Scheduling',    'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── ESG ────────────────────────────────────────────────────────
            ['module_key' => 'esg.reporting',       'domain' => 'esg', 'name' => 'ESG Reporting',                 'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'esg.carbon',          'domain' => 'esg', 'name' => 'Carbon Footprint Tracking',     'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── Real Estate ────────────────────────────────────────────────
            ['module_key' => 'realestate.assets',   'domain' => 'real-estate', 'name' => 'Property Asset Register', 'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'realestate.leases',   'domain' => 'real-estate', 'name' => 'Lease Management',        'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── Professional Services ──────────────────────────────────────
            ['module_key' => 'psa.projects',        'domain' => 'psa', 'name' => 'PSA Project Management',        'per_user_monthly_price' => 2.50, 'is_active' => false],
            ['module_key' => 'psa.billing',         'domain' => 'psa', 'name' => 'Service Billing',               'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── PLG ────────────────────────────────────────────────────────
            ['module_key' => 'plg.trials',          'domain' => 'plg', 'name' => 'Trial Management',              'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'plg.nudges',          'domain' => 'plg', 'name' => 'Product Nudges & Tours',        'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Business Travel ────────────────────────────────────────────
            ['module_key' => 'travel.booking',      'domain' => 'travel', 'name' => 'Travel Booking',             'per_user_monthly_price' => 1.50, 'is_active' => false],
            ['module_key' => 'travel.policy',       'domain' => 'travel', 'name' => 'Travel Policy Enforcement',  'per_user_monthly_price' => 1.00, 'is_active' => false],

            // ── Pricing Management ─────────────────────────────────────────
            ['module_key' => 'pricing.rules',       'domain' => 'pricing', 'name' => 'Pricing Rules Engine',      'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── Enterprise Risk ────────────────────────────────────────────
            ['module_key' => 'risk.register',       'domain' => 'risk', 'name' => 'Risk Register',                'per_user_monthly_price' => 2.00, 'is_active' => false],
            ['module_key' => 'risk.assessment',     'domain' => 'risk', 'name' => 'Risk Assessment',              'per_user_monthly_price' => 2.00, 'is_active' => false],

            // ── FP&A ───────────────────────────────────────────────────────
            ['module_key' => 'fpa.planning',        'domain' => 'fpa', 'name' => 'Financial Planning & Analysis', 'per_user_monthly_price' => 3.00, 'is_active' => false],

            // ── Workplace & Facility ───────────────────────────────────────
            ['module_key' => 'facility.desks',      'domain' => 'facility', 'name' => 'Desk Booking',             'per_user_monthly_price' => 1.00, 'is_active' => false],
            ['module_key' => 'facility.maintenance', 'domain' => 'facility', 'name' => 'Facility Maintenance',    'per_user_monthly_price' => 1.50, 'is_active' => false],
        ];
    }
}
