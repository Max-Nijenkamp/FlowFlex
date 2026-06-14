<?php

declare(strict_types=1);

namespace App\Support\Marketing;

/**
 * Static content for the Switchboard+ expansion pages (§14–24). Copy follows
 * brand voice (sentence case, no exclamation marks). Marked *(assumed)* in the
 * vault — the regenerated design bundle never landed, so these pages were
 * designed in-system; swap copy here when the final spec arrives.
 */
class MarketingContent
{
    /** @return array<int, array{slug: string, category: string, title: string, summary: string, body: array<int, string>}> */
    public static function helpArticles(): array
    {
        return [
            ['slug' => 'activate-a-module', 'category' => 'Modules & billing', 'title' => 'Activate or deactivate a module', 'summary' => 'Modules are switches — here is what flipping one actually does.', 'body' => [
                'Open Billing & modules in your workspace. Every module is a row with a switch: flip it on and it is live for your whole company immediately — navigation, permissions and billing update on their own.',
                'Flip a module off and access is gated right away, but billing only stops at the end of the current month. Your data stays exactly where it was: reactivate later and you pick up where you left off, or export it any day.',
                'Only owners and users with the billing permission can change modules.',
            ]],
            ['slug' => 'invite-your-team', 'category' => 'Getting started', 'title' => 'Invite your team', 'summary' => 'FlowFlex workspaces are invite-only — here is how people get in.', 'body' => [
                'Go to Company settings → Users and send an invitation. The invite carries the email address and the role you pick; the link works once and expires after 7 days.',
                'New colleagues set a name and password and land in your workspace — no self-registration, no domain-wide signups, no surprises on your invoice.',
                'Anyone who can sign in counts as an active user. Deactivated employees stay in your records but are never billed.',
            ]],
            ['slug' => 'understand-your-invoice', 'category' => 'Modules & billing', 'title' => 'Understand your invoice', 'summary' => 'One formula: the sum of active module prices, times active users.', 'body' => [
                'Your invoice is calculated at the start of each billing month: Σ(price of every active module) × active users. No tiers, no bundles, no minimums.',
                'The per-module price is identical at 50 users or 500. Deactivating a module stops its line at the end of the month it was switched off.',
                'The receipt in Billing & modules shows the live number at any moment — what you see there is what the next invoice says.',
            ]],
            ['slug' => 'two-factor-authentication', 'category' => 'Security', 'title' => 'Set up two-factor authentication', 'summary' => 'One minute with any authenticator app.', 'body' => [
                'Open your profile and choose Two-factor authentication. Scan the QR code with any TOTP app (1Password, Google Authenticator, Authy) and confirm with the 6-digit code.',
                'Store the recovery codes somewhere safe — each one signs you in once if you lose the device.',
                'Workspace owners can require 2FA for everyone in Company settings.',
            ]],
            ['slug' => 'export-your-data', 'category' => 'Your data', 'title' => 'Export your data', 'summary' => 'Full export, any day, no exit fee.', 'body' => [
                'Company settings → Data export builds a complete export of every module you use — records as CSV, documents as files, all in one archive.',
                'Exports are available to owners, run in the background and arrive as a download link by email.',
                'Data portability is a baseline feature, not an enterprise add-on. Cancelling never holds your data hostage: archives stay exportable for 90 days.',
            ]],
            ['slug' => 'flows-between-modules', 'category' => 'Getting started', 'title' => 'How data flows between modules', 'summary' => "These aren't integrations — it's one database behaving.", 'body' => [
                'When a deal is won, a draft invoice appears in Finance with the deal value on it. When leave is approved, scheduling unassigns the shifts. When a course is completed, the certification shows on the employee profile.',
                'There is nothing to configure, no middleware to babysit and nothing that breaks at 2am — every module reads and writes the same database.',
                'Each domain page on this site lists exactly what flows in and out of it.',
            ]],
        ];
    }

    /** @return array<string, array<string, mixed>> keyed by slug */
    public static function caseStudies(): array
    {
        return [
            'veldkamp' => [
                'slug' => 'veldkamp',
                'company' => 'Veldkamp Logistics',
                'industry' => 'Logistics · Zwolle, NL',
                'size' => '142 employees',
                'quote' => 'We cancelled nine tools in one quarter. Nobody asked for any of them back.',
                'quotee' => 'Tom de Vries, Operations director',
                'summary' => 'Veldkamp ran HR in one tool, invoicing in another, and a planning spreadsheet nobody trusted. They switched on five FlowFlex modules in week one and were off their old stack within a quarter.',
                'stats' => [
                    ['big' => '9', 'title' => 'Tools cancelled', 'body' => 'BambooHR, Xero, two planning tools and five single-purpose subscriptions — all replaced by modules.'],
                    ['big' => '€2.140', 'title' => 'Saved per month', 'body' => 'Old stack cost versus the FlowFlex invoice for 142 people with seven modules on.'],
                    ['big' => '3 wks', 'title' => 'To fully switched', 'body' => 'Employee data imported in week one, finance in week two, the old tools archived in week three.'],
                ],
                'modules' => ['Employee profiles', 'Leave & absence', 'Invoicing', 'Expenses', 'Pipeline', 'Contacts', 'Projects & boards'],
                'story' => [
                    'At 140 employees, Veldkamp had the classic patchwork: every department had bought its own tool, and operations spent Friday afternoons re-typing numbers between them. A driver calling in sick touched four systems before the schedule was correct.',
                    'They started with HR — employee profiles are free, leave was the pain. Approving leave in FlowFlex automatically flagged coverage gaps in scheduling, which made the planning spreadsheet obsolete in the first month.',
                    'Finance followed. Won deals became draft invoices on their own, and the lifetime value on each account updated when invoices were paid — numbers sales had never seen before.',
                    'The invoice did the rest of the convincing: seven modules, every price visible per user per month, and the freedom to switch any of it off. Nothing else in their stack could say that.',
                ],
            ],
        ];
    }

    /** @return array<int, array{date: string, domain: string, title: string, body: string}> newest first */
    public static function changelog(): array
    {
        return [
            ['date' => '2026-06-12', 'domain' => 'platform', 'title' => 'Switchboard+ everywhere', 'body' => 'The whole surface — site, sign-in and every panel — moved to the new design system. Plus a quick-search palette on ⌘K in every panel.'],
            ['date' => '2026-06-11', 'domain' => 'crm', 'title' => 'Custom pipelines', 'body' => 'Build your own pipeline stages per team, with per-stage win probability and slip warnings on quiet deals.'],
            ['date' => '2026-06-10', 'domain' => 'finance', 'title' => '13-week cash flow', 'body' => 'A rolling cash view that reads open invoices, bills and payroll runs — no spreadsheet export required.'],
            ['date' => '2026-06-08', 'domain' => 'hr', 'title' => 'Leave balances that explain themselves', 'body' => 'Every balance shows its math: accrued, taken, pending and carried over, per leave type.'],
            ['date' => '2026-06-05', 'domain' => 'platform', 'title' => 'Full data export', 'body' => 'One archive with every record and document, built in the background, delivered by email. Available to owners on every plan.'],
            ['date' => '2026-06-02', 'domain' => 'projects', 'title' => 'Boards read the leave calendar', 'body' => 'Assignments now warn when the assignee is on approved leave during the task window.'],
        ];
    }
}
