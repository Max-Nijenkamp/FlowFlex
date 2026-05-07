<?php

namespace Database\Seeders\Marketing;

use App\Models\Marketing\HelpArticle;
use App\Models\Marketing\HelpCategory;
use Illuminate\Database\Seeder;

class HelpArticleSeeder extends Seeder
{
    public function run(): void
    {
        $gettingStarted = HelpCategory::where('slug', 'getting-started')->first();
        $hrModule       = HelpCategory::where('slug', 'hr-module')->first();
        $billing        = HelpCategory::where('slug', 'billing-plans')->first();

        $articles = [
            // Getting Started (2 articles)
            [
                'help_category_id' => $gettingStarted?->id,
                'title'            => 'How to Invite Your Team to FlowFlex',
                'slug'             => 'how-to-invite-your-team',
                'body'             => "Once your workspace is set up, you can invite team members from the Workspace Settings page.\n\n1. Navigate to **Workspace Settings** from the left sidebar.\n2. Click **Team Members**, then **Invite Member**.\n3. Enter the email address of the person you want to invite.\n4. Assign a role: Owner, Admin, or Member.\n5. Click **Send Invitation**.\n\nThe invitee will receive an email with a secure link to accept the invitation and set their password. Invitations expire after 72 hours. If the link expires, you can resend it from the Team Members page.",
                'seo_title'        => 'How to Invite Team Members to FlowFlex',
                'seo_description'  => 'Step-by-step guide to inviting colleagues to your FlowFlex workspace.',
                'is_published'     => true,
                'helpful_count'    => 0,
                'not_helpful_count' => 0,
            ],
            [
                'help_category_id' => $gettingStarted?->id,
                'title'            => 'Enabling and Disabling Modules',
                'slug'             => 'enabling-and-disabling-modules',
                'body'             => "FlowFlex is modular — you only activate (and pay for) the features you need.\n\n**To enable a module:**\n1. Go to **Workspace Settings > Modules**.\n2. Find the module you want to activate.\n3. Click **Enable** and confirm. A 30-day free trial starts immediately.\n\n**To disable a module:**\n1. Go to **Workspace Settings > Modules**.\n2. Click the active module and select **Disable**.\n3. Confirm the action. Billing stops at the end of the current billing period.\n\n**Important:** Disabling a module hides it from your workspace but does not delete any data. If you re-enable the module in the future, all your data will still be there.",
                'seo_title'        => 'How to Enable and Disable FlowFlex Modules',
                'seo_description'  => 'Learn how to activate and deactivate modules in your FlowFlex workspace.',
                'is_published'     => true,
                'helpful_count'    => 0,
                'not_helpful_count' => 0,
            ],
            // HR Module (2 articles)
            [
                'help_category_id' => $hrModule?->id,
                'title'            => 'Adding and Managing Employee Records',
                'slug'             => 'adding-and-managing-employee-records',
                'body'             => "Employee records are the foundation of the HR module. Each record stores personal details, employment information, contract data, and any custom fields you have configured.\n\n**Adding a new employee:**\n1. Navigate to **HR > Employees** and click **Add Employee**.\n2. Fill in the required fields: first name, last name, email address, start date, and department.\n3. Optionally add contract type, job title, salary, and manager.\n4. Click **Save**. An onboarding flow will be triggered automatically if you have configured one for the department.\n\n**Editing an employee record:**\nClick any employee in the list to open their profile. All sections are inline-editable. Changes are logged automatically in the activity trail.\n\n**Archiving an employee:**\nWhen an employee leaves, use the **Archive** action from their profile. Archived employees are soft-deleted — their records are retained for the legally required retention period but hidden from active views.",
                'seo_title'        => 'Managing Employee Records in FlowFlex HR',
                'seo_description'  => 'How to add, edit, and archive employee records in the FlowFlex HR module.',
                'is_published'     => true,
                'helpful_count'    => 0,
                'not_helpful_count' => 0,
            ],
            [
                'help_category_id' => $hrModule?->id,
                'title'            => 'Approving and Declining Leave Requests',
                'slug'             => 'approving-and-declining-leave-requests',
                'body'             => "When an employee submits a leave request, their manager receives an email notification and an in-app notification. Leave requests can be approved or declined from several places in FlowFlex.\n\n**From the notification:**\nClick the notification to open the leave request directly. Review the dates and any notes from the employee, then click **Approve** or **Decline**.\n\n**From the HR module:**\n1. Go to **HR > Leave Requests**.\n2. Use the filter to show **Pending** requests.\n3. Click a request to open it, then approve or decline.\n\n**From the employee profile:**\nOpen the employee's profile and navigate to the **Leave** tab. All leave requests — past and pending — are visible here.\n\n**Adding a note when declining:**\nWhen declining a request, you can add an optional note explaining the reason. This note is visible to the employee.",
                'seo_title'        => 'How to Approve or Decline Leave Requests in FlowFlex',
                'seo_description'  => 'Step-by-step guide for managers to approve or decline employee leave requests in FlowFlex.',
                'is_published'     => true,
                'helpful_count'    => 0,
                'not_helpful_count' => 0,
            ],
            // Billing & Plans (2 articles)
            [
                'help_category_id' => $billing?->id,
                'title'            => 'Understanding FlowFlex Module Billing',
                'slug'             => 'understanding-module-billing',
                'body'             => "FlowFlex uses a per-module, per-seat pricing model. Here is how it works:\n\n**Per-seat calculation:** The monthly charge for a module is the per-seat price multiplied by the number of active employees in your workspace at the start of the billing period.\n\n**Module trials:** Every module starts with a free 30-day trial. Your first invoice for a module covers the period after the trial ends.\n\n**Mid-month changes:** If you add employees during the billing month, the additional seats are pro-rated from the date the employee was added.\n\n**Invoices:** Invoices are generated on the 1st of each month and sent to the billing email address on your account. PDF copies are always available in **Workspace Settings > Billing**.\n\n**Payment methods:** FlowFlex accepts all major credit and debit cards via Stripe. SEPA direct debit is available for EU customers upon request.",
                'seo_title'        => 'How FlowFlex Billing Works | Module Pricing Explained',
                'seo_description'  => 'Understand how FlowFlex module billing, per-seat pricing, and invoicing work.',
                'is_published'     => true,
                'helpful_count'    => 0,
                'not_helpful_count' => 0,
            ],
            [
                'help_category_id' => $billing?->id,
                'title'            => 'Updating Your Payment Method',
                'slug'             => 'updating-your-payment-method',
                'body'             => "You can update the payment method associated with your FlowFlex account at any time.\n\n1. Go to **Workspace Settings > Billing**.\n2. Click **Payment Methods**.\n3. Click **Add Payment Method** and enter your new card details. Payments are processed securely via Stripe — FlowFlex never stores card numbers.\n4. Once added, set the new card as the default by clicking **Set as Default**.\n5. Optionally remove the old card.\n\n**What if my payment fails?**\nIf a payment fails, FlowFlex will retry automatically after 3 and 7 days. You will receive an email notification each time. If payment is not received after 14 days, module access is suspended until the outstanding invoice is settled. No data is deleted during a suspension.",
                'seo_title'        => 'How to Update Your Payment Method in FlowFlex',
                'seo_description'  => 'Step-by-step guide to changing the credit card or payment method on your FlowFlex account.',
                'is_published'     => true,
                'helpful_count'    => 0,
                'not_helpful_count' => 0,
            ],
        ];

        foreach ($articles as $data) {
            HelpArticle::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
