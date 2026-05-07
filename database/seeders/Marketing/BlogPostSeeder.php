<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\BlogCategory;
use App\Models\Marketing\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $productUpdates  = BlogCategory::where('slug', 'product-updates')->first();
        $industryInsights = BlogCategory::where('slug', 'industry-insights')->first();
        $howToGuides     = BlogCategory::where('slug', 'how-to-guides')->first();
        $customerStories = BlogCategory::where('slug', 'customer-stories')->first();

        $posts = [
            // Published — Product Updates
            [
                'blog_category_id' => $productUpdates?->id,
                'title'            => 'Introducing the FlowFlex HR Module: Smarter People Management',
                'slug'             => 'introducing-flowflex-hr-module',
                'excerpt'          => 'Today we are thrilled to announce the general availability of the FlowFlex HR module — bringing employee records, leave management, and payroll into one unified platform.',
                'body'             => $this->hrModuleBody(),
                'tags'             => ['hr', 'product', 'launch'],
                'status'           => 'published',
                'published_at'     => now()->subDays(30),
                'seo_title'        => 'FlowFlex HR Module Launch | Smarter People Management',
                'seo_description'  => 'FlowFlex HR module is now live. Manage employees, leave requests, and payroll in one place.',
            ],
            // Published — Product Updates
            [
                'blog_category_id' => $productUpdates?->id,
                'title'            => 'Projects Module Update: Time Tracking and Timesheets Are Here',
                'slug'             => 'projects-module-time-tracking-update',
                'excerpt'          => 'We have shipped a major update to the Projects module, adding built-in time tracking and timesheet approvals to help teams stay on budget.',
                'body'             => $this->projectsUpdateBody(),
                'tags'             => ['projects', 'time-tracking', 'product'],
                'status'           => 'published',
                'published_at'     => now()->subDays(14),
                'seo_title'        => 'Projects Module Update: Time Tracking | FlowFlex',
                'seo_description'  => 'Track time and approve timesheets directly in FlowFlex. Available now in the Projects module.',
            ],
            // Published — Industry Insights
            [
                'blog_category_id' => $industryInsights?->id,
                'title'            => 'Why Dutch SMEs Are Moving Away From Spreadsheet HR',
                'slug'             => 'dutch-smes-moving-away-from-spreadsheet-hr',
                'excerpt'          => 'Research shows that over 60% of Dutch SMEs still manage HR processes in spreadsheets. Here is why that is a problem — and what forward-thinking companies are doing instead.',
                'body'             => $this->industryInsightsBody(),
                'tags'             => ['hr', 'netherlands', 'sme', 'insights'],
                'status'           => 'published',
                'published_at'     => now()->subDays(21),
                'seo_title'        => 'Why Dutch SMEs Need Modern HR Software | FlowFlex Blog',
                'seo_description'  => 'Over 60% of Dutch SMEs still use spreadsheets for HR. Discover why this is risky and how to upgrade.',
            ],
            // Published — How-To Guides
            [
                'blog_category_id' => $howToGuides?->id,
                'title'            => 'How to Set Up Your First Onboarding Flow in FlowFlex',
                'slug'             => 'how-to-set-up-onboarding-flow-flowflex',
                'excerpt'          => 'A great onboarding experience sets new hires up for success. This guide walks you through creating your first automated onboarding flow in FlowFlex in under 30 minutes.',
                'body'             => $this->howToOnboardingBody(),
                'tags'             => ['onboarding', 'how-to', 'hr'],
                'status'           => 'published',
                'published_at'     => now()->subDays(7),
                'seo_title'        => 'How to Set Up Onboarding in FlowFlex | Step-by-Step Guide',
                'seo_description'  => 'Set up automated onboarding flows in FlowFlex. Get new hires up to speed faster with this step-by-step guide.',
            ],
            // Draft — Customer Stories
            [
                'blog_category_id' => $customerStories?->id,
                'title'            => 'How Bakkerij Cornelissen Saved 10 Hours a Week with FlowFlex',
                'slug'             => 'bakkerij-cornelissen-saved-10-hours-a-week',
                'excerpt'          => 'Bakkerij Cornelissen, a 45-person family bakery in Utrecht, was drowning in paper leave forms. FlowFlex changed everything.',
                'body'             => $this->customerStoryBody(),
                'tags'             => ['customer-story', 'case-study', 'hr'],
                'status'           => 'draft',
                'published_at'     => null,
            ],
            // Draft — Industry Insights
            [
                'blog_category_id' => $industryInsights?->id,
                'title'            => 'The True Cost of Manual Payroll Processing in 2026',
                'slug'             => 'true-cost-of-manual-payroll-processing-2026',
                'excerpt'          => 'Manual payroll is not just slow — it is expensive. We break down the hidden costs that SMEs overlook every month.',
                'body'             => $this->payrollCostBody(),
                'tags'             => ['payroll', 'insights', 'costs'],
                'status'           => 'draft',
                'published_at'     => null,
            ],
        ];

        foreach ($posts as $data) {
            BlogPost::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }

    private function hrModuleBody(): string
    {
        return <<<'BODY'
Managing people is the heartbeat of any organisation. Yet for most small and medium-sized businesses, HR processes are scattered across spreadsheets, email inboxes, and sticky notes. That changes today.

The FlowFlex HR module brings everything you need to manage your workforce into a single, clean interface — right alongside the rest of your business operations.

**What is included in the HR module?**

The initial release ships with four core areas:

*Employee Records* — Maintain a complete, searchable directory of every employee in your company. Store personal details, employment history, contract information, and custom fields you define yourself. Everything is encrypted at rest and access-controlled by role.

*Leave Management* — Employees can request time off directly from their dashboard. Managers receive instant notifications and can approve or decline in a single click. Accrual rules, carry-over policies, and public holidays are all configurable per company.

*Department Management* — Organise your workforce into departments and sub-departments. Assign managers, set headcount targets, and get a clear picture of your org structure at any time.

*Onboarding Flows* — Build templated onboarding checklists that automatically trigger when a new hire joins. Assign tasks to the new employee, their manager, or your IT team. No more missed laptop orders or forgotten introductions.

**Who is this for?**

The HR module is designed for Dutch and European SMEs with between 10 and 500 employees. It handles the complexity of Dutch employment law, including WW, vacation day accrual under Dutch civil law, and GDPR-compliant data retention.

**What is coming next?**

Phase 2 of the HR module — due in Q3 2026 — adds payroll processing, salary benchmarking, and performance reviews. If you want early access, join our beta waitlist from within your FlowFlex dashboard.

Getting started is simple. Head to Workspace Settings, enable the HR module, and follow the setup wizard. The whole thing takes less than 15 minutes.

We cannot wait to see what you build with it.
BODY;
    }

    private function projectsUpdateBody(): string
    {
        return <<<'BODY'
Project managers have been asking for time tracking since we launched the Projects module in early 2025. Today, we are delivering exactly that — and a whole lot more.

**Time Tracking**

Every task in FlowFlex can now have time entries logged against it. Teammates can start a running timer directly from the task view, or log time manually after the fact. Time entries are linked to the task, the project, and the assignee, making reporting straightforward.

**Timesheets**

At the end of each week, FlowFlex automatically compiles a timesheet for each team member, grouped by project and task. Managers can review and approve timesheets in a dedicated approval flow. Approved timesheets feed directly into the billing and payroll modules if those are enabled for your workspace.

**Project Budget Tracking**

If you set an estimated budget or hour cap on a project, FlowFlex now shows you a real-time progress bar as time is logged. You will receive a notification when a project reaches 80% of its budget, giving you time to act before you go over.

**Export and Reporting**

Time entries and timesheets can be exported to CSV or PDF at any time. Filters for date range, project, employee, and approval status make it easy to produce the exact slice of data you need.

**How to enable time tracking**

Time tracking is enabled by default for all existing Projects module customers. Head to the Projects settings page in your workspace to configure rounding rules, overtime policies, and approval workflows.

If you have not enabled the Projects module yet, visit Workspace Settings to add it to your plan. The first 30 days are included free as part of any module trial.

We are already working on the next iteration: resource planning and capacity forecasting. Subscribe to the changelog to stay up to date.
BODY;
    }

    private function industryInsightsBody(): string
    {
        return <<<'BODY'
A survey conducted by Berenschot in late 2025 found that 63% of Dutch companies with fewer than 250 employees still rely on Microsoft Excel or Google Sheets as their primary HR tool. That statistic might surprise you — or it might not, if you work in HR at a Dutch SME.

**Why spreadsheets are so persistent**

Spreadsheets are free, familiar, and flexible. An HR manager who has spent years perfecting a leave-tracking workbook is not going to abandon it without a compelling reason. And until recently, the available HR software options were either too expensive, too complex, or simply not built for the Dutch market.

**The hidden costs of spreadsheet HR**

The obvious cost is time. Maintaining employee records, processing leave requests, and preparing payroll reports in spreadsheets is slow work. But the less visible costs are often larger.

*Compliance risk.* Dutch employment law is detailed and changes regularly. A spreadsheet does not send you a reminder when the WW contribution rate changes, or when an employee's probation period is about to end. Mistakes can lead to fines from the Dutch Tax Authority or labour disputes.

*Data leakage.* Spreadsheets with salary data and BSN numbers are frequently shared by email or stored on personal computers. Under the GDPR, this is a serious liability. The Dutch Data Protection Authority (AP) has levied fines against companies for exactly this kind of careless data handling.

*Knowledge concentration.* When the person who built the spreadsheet leaves, critical institutional knowledge often goes with them. A cloud-based HR system ensures that processes are documented, auditable, and transferable.

**What leading Dutch SMEs are doing instead**

The companies moving away from spreadsheet HR are not all tech companies. We are seeing traditional industries — logistics, construction, food production — make the switch. Their motivations differ, but the outcomes are consistent: fewer compliance errors, faster processes, and HR managers who can focus on strategic work rather than data entry.

If you are still running HR from a spreadsheet, now is the time to explore what a modern, Dutch-built alternative looks like.
BODY;
    }

    private function howToOnboardingBody(): string
    {
        return <<<'BODY'
First impressions matter. Research consistently shows that employees who experience a structured onboarding process are more productive sooner, more likely to stay with the company, and more likely to recommend the organisation to others. Yet most SMEs still onboard new hires via a series of ad-hoc emails and verbal instructions.

FlowFlex Onboarding Flows change that. Here is how to set one up in under 30 minutes.

**Step 1: Open the Onboarding section**

From the HR module, navigate to Onboarding Templates in the left sidebar. You will see a list of any existing templates, or an empty state if this is your first time here.

**Step 2: Create a new template**

Click New Template and give it a descriptive name — for example, "Standard Office Employee Onboarding" or "Remote Engineer Onboarding". You can create as many templates as you need for different roles or locations.

**Step 3: Add tasks**

Each template consists of a list of tasks. For each task, you define:
- A title and description
- Who is responsible (the new hire, their manager, IT, or HR)
- When it should be completed (relative to the start date, e.g., "Day 1", "Week 1", "Day 30")
- Whether it is required or optional

Some examples of useful tasks: Send laptop order (IT, Day -7), Prepare workspace (Facilities, Day -1), Sign employment contract (New Hire, Day 1), Meet with direct manager (New Hire, Day 1), Complete security training (New Hire, Week 1).

**Step 4: Link the template to a department or role**

You can set a default template for a department so that it automatically applies whenever a new employee is added to that department. Or you can choose a template manually when creating an employee record.

**Step 5: Trigger the flow**

When you create a new employee with a start date, FlowFlex asks which onboarding template to apply. Once confirmed, all tasks are created automatically, assigned to the right people, and visible in their dashboards.

**Tracking progress**

From the employee's profile, you can see a real-time checklist of outstanding and completed onboarding tasks. Managers receive a weekly digest of any tasks that are overdue.

That is all there is to it. Once your first template is set up, you can reuse it for every new hire with a single click.
BODY;
    }

    private function customerStoryBody(): string
    {
        return <<<'BODY'
Bakkerij Cornelissen has been baking bread in Utrecht since 1978. With 45 employees spread across two shifts and three locations, keeping track of leave requests, schedules, and contracts had become a full-time job in itself — one that fell to co-owner and operations manager Liesbeth Cornelissen.

"We were using paper forms for leave requests," Liesbeth tells us. "Employees would fill them in and hand them to their supervisor, who would call me. I would check the calendar in my office, which was also a paper calendar. Then I would call the supervisor back. It took days sometimes."

When a new hire misunderstood the process and took a week off without approval, Liesbeth decided enough was enough. She found FlowFlex through a recommendation in a Dutch bakery industry forum.

"I was sceptical. We are a bakery, not a tech company. But the setup was genuinely straightforward. I had employee records imported and leave management running within a day."

The results were immediate. Leave requests are now submitted and approved through the FlowFlex mobile interface. Employees can see their remaining balance in real time. Supervisors approve or decline with a single tap.

"I estimate I save at least 10 hours a week. That is time I can spend on the bakery — on product, on customers, on training our team."

Bakkerij Cornelissen has since enabled the Onboarding module and is piloting the Documents module for contract management. "Once you have one module set up, adding the next one is easy. Everything talks to everything else."

If you run a business in the food and hospitality sector and want to understand how FlowFlex fits your needs, book a 30-minute demo. We will show you exactly what is possible.
BODY;
    }

    private function payrollCostBody(): string
    {
        return <<<'BODY'
Most business owners know that payroll takes time. Fewer have calculated exactly how much it costs them. When we spoke to 50 Dutch SME owners and HR managers earlier this year, the average time spent on payroll processing was 8.4 hours per month. For a company where that work is done by someone earning EUR 45 per hour, that is nearly EUR 4,500 per year in labour cost alone — before accounting for errors.

**The cost of errors**

Manual payroll errors are far more common than most companies admit. Over-payments, under-payments, incorrect pension deductions, and late filings with the Belastingdienst all carry real costs. A single error discovered during a payroll audit can cost thousands of euros to investigate and correct.

**The compliance burden**

Dutch payroll is complex. Loonheffingen, WW premiums, WIA contributions, holiday allowance calculations, and the differences between permanent and flex contracts all need to be handled correctly and consistently. Legislation changes regularly. Keeping a manual process up to date requires continuous attention.

**The opportunity cost**

Perhaps the biggest hidden cost is the opportunity cost. Every hour your HR manager spends cross-referencing timesheets in Excel is an hour not spent on retention, recruitment, culture, or the strategic work that actually moves your business forward.

**What modern payroll tools deliver**

A payroll module integrated with your HR and time tracking data eliminates most manual data entry. Hours worked flow from timesheets to payroll automatically. Leave deductions are applied based on approved requests. The system flags anomalies before you file — not after.

We will share more on the FlowFlex payroll module roadmap in an upcoming post. In the meantime, if you would like to discuss your current payroll setup, get in touch with our team.
BODY;
    }
}
