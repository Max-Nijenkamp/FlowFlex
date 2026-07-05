<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

/**
 * Seeds the CORE permission universe (web guard). Idempotent — upserts by
 * (name, guard). Domain permissions (hr.*, finance.*, crm.*, …) arrive with
 * their modules and are appended to this const then.
 */
class PermissionSeeder extends Seeder
{
    /** @var array<string> */
    public const PERMISSIONS = [
        // core.company-settings
        'core.settings.view',
        'core.settings.manage',
        // core.rbac
        'core.rbac.view-any',
        'core.rbac.create',
        'core.rbac.update',
        'core.rbac.delete',
        'core.rbac.assign-roles',
        'core.rbac.transfer-ownership',
        // core.invitations
        'core.invitations.view-any',
        'core.invitations.send',
        'core.invitations.resend',
        'core.invitations.revoke',
        // core.billing-engine
        'core.billing.view',
        'core.billing.manage',
        'core.billing.activate-module',
        'core.billing.deactivate-module',
        // core.module-marketplace
        'core.marketplace.view',
        'core.marketplace.manage',
        // core.audit-log
        'core.audit.view-any',
        'core.audit.view',
        // core.files
        'core.files.view-any',
        'core.files.manage',
        // core.data-import / export surfaces
        'core.import.create',
        'core.import.view-any',
        // core.data-privacy
        'core.privacy.view-any',
        'core.privacy.export',
        'core.privacy.erase',
        // core.notifications (preferences are self-service; manage = company defaults)
        'core.notifications.manage',
        // core.api-clients
        'core.api.view-any',
        'core.api.create',
        'core.api.rotate',
        'core.api.revoke',
        // core.webhooks
        'core.webhooks.view-any',
        'core.webhooks.manage',
        'core.webhooks.test',
        'core.webhooks.rotate',
        // core.setup-wizard
        'core.setup.complete',
        // core.workspace-hub
        'core.hub.view',
        // domain panel access
        'access.hr',
        'access.finance',
        'access.crm',
        // crm.contacts
        'crm.contacts.view-any',
        'crm.contacts.view',
        'crm.contacts.create',
        'crm.contacts.update',
        'crm.contacts.delete',
        'crm.contacts.merge',
        'crm.contacts.change-lifecycle',
        'crm.contacts.export',
        'crm.accounts.manage',
        // crm.deals
        'crm.deals.view-any',
        'crm.deals.view',
        'crm.deals.create',
        'crm.deals.update',
        'crm.deals.view-all',
        // crm.pipeline
        'crm.pipeline.view',
        'crm.pipeline.manage',
        // crm.activities
        'crm.activities.view-any',
        'crm.activities.create',
        'crm.activities.update',
        'crm.activities.delete',
        // finance.ledger
        'finance.ledger.view',
        'finance.ledger.post-manual',
        'finance.ledger.manage-periods',
        // finance.invoicing
        'finance.invoices.view-any',
        'finance.invoices.view',
        'finance.invoices.create',
        'finance.invoices.update',
        'finance.invoices.send',
        'finance.invoices.void',
        'finance.invoices.record-payment',
        // finance.bank
        'finance.bank.view',
        'finance.bank.manage',
        'finance.bank.reconcile',
        // finance.expenses
        'finance.expenses.view-any',
        'finance.expenses.view',
        'finance.expenses.create',
        'finance.expenses.approve',
        'finance.expenses.manage-policy',
        // hr.profiles
        'hr.employees.view-any',
        'hr.employees.view',
        'hr.employees.create',
        'hr.employees.update',
        'hr.employees.delete',
        'hr.employees.offboard',
        // hr.leave
        'hr.leave.view-any',
        'hr.leave.view',
        'hr.leave.create',
        'hr.leave.approve',
        'hr.leave.reject',
        'hr.leave.manage-types',
        // hr.onboarding
        'hr.onboarding.manage',
        'hr.onboarding.view-any',
        // hr self-service (default for every employee)
        'hr.self-service.view',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Artisan::call('permission:cache-reset');
    }
}
