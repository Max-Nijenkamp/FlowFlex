<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

/**
 * Creates every `domain.module.action` permission string. Idempotent — safe to
 * re-run after each deploy that adds a domain. Each module spec's ## Permissions
 * section is the source; this baseline grows as modules ship.
 */
class PermissionSeeder extends Seeder
{
    /** @var list<string> */
    public const array PERMISSIONS = [
        // core.settings
        'core.settings.view',
        'core.settings.update',
        // core.rbac
        'core.rbac.view-any',
        'core.rbac.view',
        'core.rbac.create',
        'core.rbac.update',
        'core.rbac.delete',
        'core.rbac.assign',
        // core.invitations
        'core.invitations.view-any',
        'core.invitations.create',
        'core.invitations.resend',
        'core.invitations.revoke',
        // core.billing
        'core.billing.view',
        'core.billing.activate-module',
        'core.billing.deactivate-module',
        'core.billing.manage-payment-method',
        // core.marketplace
        'core.marketplace.view',
        // core.audit
        'core.audit.view-any',
        'core.audit.export',
        // core.notifications
        'core.notifications.manage-own',
        // core.files
        'core.files.view-any',
        'core.files.upload',
        'core.files.delete',
        // core.import
        'core.import.view-any',
        'core.import.create',
        // core.webhooks
        'core.webhooks.view-any',
        'core.webhooks.manage',
        // core.api
        'core.api.view-any',
        'core.api.manage',
        // core.setup
        'core.setup.run',
        // core.privacy
        'core.privacy.view-any',
        'core.privacy.manage',
        // core.i18n
        'core.i18n.view',
        'core.i18n.update',
        // core.health
        'core.health.view',
        // panel access
        'access.hr-panel',
        'access.finance-panel',
        'access.crm-panel',
        // hr.profiles
        'hr.employees.view-any',
        'hr.employees.view',
        'hr.employees.create',
        'hr.employees.update',
        'hr.employees.delete',
        'hr.employees.offboard',
        'hr.employees.view-sensitive',
        'hr.departments.manage',
        // hr.org
        'hr.org.view',
        'hr.org.reassign',
        // hr.self-service
        'hr.self-service.view',
        'hr.self-service.update-own',
        // hr.leave
        'hr.leave.view-any',
        'hr.leave.view',
        'hr.leave.create',
        'hr.leave.update',
        'hr.leave.delete',
        'hr.leave.approve',
        'hr.leave.reject',
        'hr.leave.manage-types',
        // hr.onboarding
        'hr.onboarding.view-any',
        'hr.onboarding.view',
        'hr.onboarding.create',
        'hr.onboarding.update',
        'hr.onboarding.complete-task',
        'hr.onboarding.manage-templates',
        // hr.payroll
        'hr.payroll.view-any',
        'hr.payroll.view',
        'hr.payroll.create',
        'hr.payroll.process',
        'hr.payroll.approve',
        'hr.payroll.archive',
        'hr.payroll.manage-deductions',
        'hr.payroll.view-sensitive',
        // hr v1 remaining
        'hr.recruitment.view-any',
        'hr.recruitment.create',
        'hr.recruitment.update',
        'hr.recruitment.hire',
        'hr.recruitment.manage-offers',
        'hr.performance.view-any',
        'hr.performance.view',
        'hr.performance.submit',
        'hr.performance.manage-cycles',
        'hr.performance.calibrate',
        'hr.time.view-any',
        'hr.time.view',
        'hr.time.log-own',
        'hr.time.submit-own',
        'hr.time.approve',
        'hr.time.manage',
        'hr.shifts.view-any',
        'hr.shifts.view',
        'hr.shifts.create',
        'hr.shifts.update',
        'hr.shifts.publish',
        'hr.shifts.request-swap',
        'hr.shifts.approve-swap',
        'hr.compensation.view-any',
        'hr.compensation.manage-bands',
        'hr.compensation.adjust-salary',
        'hr.compensation.manage-benefits',
        'hr.compensation.enroll',
        'hr.analytics.view',
        'hr.workforce.view-any',
        'hr.workforce.create',
        'hr.workforce.update',
        'hr.workforce.approve-role',
        'hr.feedback.view-any',
        'hr.feedback.give',
        'hr.feedback.view-own',
        'hr.feedback.one-on-one',
        'hr.dei.view-dashboard',
        'hr.dei.submit-own',
        // finance
        'finance.ledger.view-any',
        'finance.ledger.post',
        'finance.ledger.close-period',
        'finance.invoices.view-any',
        'finance.invoices.create',
        'finance.invoices.update',
        'finance.invoices.send',
        'finance.invoices.record-payment',
        'finance.expenses.view-any',
        'finance.expenses.create',
        'finance.expenses.approve',
        'finance.expenses.reject',
        'finance.bank.view-any',
        'finance.bank.manage',
        // finance v1 remaining
        'finance.ar.view',
        'finance.ar.manage-dunning',
        'finance.ar.write-off',
        'finance.ar.allocate-payment',
        'finance.ap.view-any',
        'finance.ap.create',
        'finance.ap.approve',
        'finance.ap.approve-large',
        'finance.ap.schedule',
        'finance.ap.execute-run',
        'finance.ap.manage-suppliers',
        'finance.ap.view-sensitive',
        'finance.budgets.view-any',
        'finance.budgets.create',
        'finance.budgets.update',
        'finance.budgets.approve',
        'finance.reporting.view',
        'finance.reporting.export',
        'finance.tax.view',
        'finance.tax.manage-rates',
        'finance.tax.file-period',
        'finance.cashflow.view',
        'finance.cashflow.manage-items',
        'finance.assets.view-any',
        'finance.assets.create',
        'finance.assets.update',
        'finance.assets.run-depreciation',
        'finance.assets.dispose',
        'finance.forecasting.view-any',
        'finance.forecasting.create',
        'finance.forecasting.update',
        'finance.currency.view',
        'finance.currency.manage',
        // crm
        'crm.contacts.view-any',
        'crm.contacts.create',
        'crm.contacts.update',
        'crm.contacts.delete',
        'crm.deals.view-any',
        'crm.deals.create',
        'crm.deals.update',
        'crm.deals.delete',
        'crm.deals.win',
        'crm.deals.lose',
        'crm.pipeline.view',
        'crm.activities.view-any',
        'crm.activities.create',
        'crm.quotes.view-any',
        'crm.quotes.create',
        'crm.quotes.send',
        'crm.quotes.accept',
    ];

    public function run(): void
    {
        Artisan::call('permission:cache-reset');

        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }
}
