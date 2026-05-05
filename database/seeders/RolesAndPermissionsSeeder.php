<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // All permissions scoped to the 'tenant' guard — the guard Tenants use for workspace panels.
        // Naming convention: {module}.{resource}.{action}
        $permissions = [
            // ── Platform / Admin (internal staff only, but tracked for completeness) ──
            'platform.users.view',
            'platform.users.create',
            'platform.users.edit',
            'platform.users.delete',

            'platform.companies.view',
            'platform.companies.create',
            'platform.companies.edit',
            'platform.companies.delete',

            'platform.tenants.view',
            'platform.tenants.create',
            'platform.tenants.edit',
            'platform.tenants.delete',

            'platform.roles.view',
            'platform.roles.create',
            'platform.roles.edit',
            'platform.roles.delete',

            'platform.permissions.view',

            // ── Workspace ──
            'workspace.settings.view',
            'workspace.settings.edit',
            'workspace.billing.view',
            'workspace.billing.edit',
            'workspace.modules.view',
            'workspace.modules.edit',

            // ── HR ──
            'hr.employees.view',
            'hr.employees.create',
            'hr.employees.edit',
            'hr.employees.delete',

            'hr.onboarding.view',
            'hr.onboarding.create',
            'hr.onboarding.edit',
            'hr.onboarding.delete',

            'hr.offboarding.view',
            'hr.offboarding.create',
            'hr.offboarding.edit',
            'hr.offboarding.delete',

            'hr.leave.view',
            'hr.leave.create',
            'hr.leave.edit',
            'hr.leave.delete',
            'hr.leave.approve',

            'hr.payroll.view',
            'hr.payroll.create',
            'hr.payroll.edit',
            'hr.payroll.delete',
            'hr.payroll.run',

            'hr.performance.view',
            'hr.performance.create',
            'hr.performance.edit',
            'hr.performance.delete',

            'hr.recruitment.view',
            'hr.recruitment.create',
            'hr.recruitment.edit',
            'hr.recruitment.delete',

            'hr.scheduling.view',
            'hr.scheduling.create',
            'hr.scheduling.edit',
            'hr.scheduling.delete',

            'hr.benefits.view',
            'hr.benefits.create',
            'hr.benefits.edit',
            'hr.benefits.delete',

            'hr.feedback.view',
            'hr.feedback.create',
            'hr.feedback.edit',
            'hr.feedback.delete',

            'hr.compliance.view',
            'hr.compliance.edit',

            // ── Projects ──
            'projects.tasks.view',
            'projects.tasks.create',
            'projects.tasks.edit',
            'projects.tasks.delete',

            'projects.projects.view',
            'projects.projects.create',
            'projects.projects.edit',
            'projects.projects.delete',

            'projects.time.view',
            'projects.time.create',
            'projects.time.edit',
            'projects.time.delete',
            'projects.time.approve',

            'projects.documents.view',
            'projects.documents.create',
            'projects.documents.edit',
            'projects.documents.delete',

            'projects.approvals.view',
            'projects.approvals.create',
            'projects.approvals.edit',
            'projects.approvals.approve',

            'projects.wiki.view',
            'projects.wiki.create',
            'projects.wiki.edit',
            'projects.wiki.delete',

            'projects.sprints.view',
            'projects.sprints.create',
            'projects.sprints.edit',
            'projects.sprints.delete',

            // ── Finance ──
            'finance.invoices.view',
            'finance.invoices.create',
            'finance.invoices.edit',
            'finance.invoices.delete',
            'finance.invoices.send',

            'finance.expenses.view',
            'finance.expenses.create',
            'finance.expenses.edit',
            'finance.expenses.delete',
            'finance.expenses.approve',

            'finance.bills.view',
            'finance.bills.create',
            'finance.bills.edit',
            'finance.bills.delete',

            'finance.banking.view',
            'finance.banking.edit',

            'finance.budgets.view',
            'finance.budgets.create',
            'finance.budgets.edit',
            'finance.budgets.delete',

            'finance.reports.view',
            'finance.reports.export',

            'finance.tax.view',
            'finance.tax.edit',

            // ── CRM ──
            'crm.contacts.view',
            'crm.contacts.create',
            'crm.contacts.edit',
            'crm.contacts.delete',

            'crm.pipeline.view',
            'crm.pipeline.create',
            'crm.pipeline.edit',
            'crm.pipeline.delete',

            'crm.quotes.view',
            'crm.quotes.create',
            'crm.quotes.edit',
            'crm.quotes.delete',
            'crm.quotes.send',

            'crm.inbox.view',
            'crm.inbox.create',
            'crm.inbox.edit',
            'crm.inbox.delete',

            'crm.support.view',
            'crm.support.create',
            'crm.support.edit',
            'crm.support.delete',
            'crm.support.assign',

            // ── Marketing ──
            'marketing.cms.view',
            'marketing.cms.create',
            'marketing.cms.edit',
            'marketing.cms.delete',
            'marketing.cms.publish',

            'marketing.email.view',
            'marketing.email.create',
            'marketing.email.edit',
            'marketing.email.delete',
            'marketing.email.send',

            'marketing.social.view',
            'marketing.social.create',
            'marketing.social.edit',
            'marketing.social.delete',
            'marketing.social.publish',

            'marketing.forms.view',
            'marketing.forms.create',
            'marketing.forms.edit',
            'marketing.forms.delete',

            // ── Operations ──
            'operations.inventory.view',
            'operations.inventory.create',
            'operations.inventory.edit',
            'operations.inventory.delete',

            'operations.purchasing.view',
            'operations.purchasing.create',
            'operations.purchasing.edit',
            'operations.purchasing.delete',
            'operations.purchasing.approve',

            'operations.assets.view',
            'operations.assets.create',
            'operations.assets.edit',
            'operations.assets.delete',

            'operations.maintenance.view',
            'operations.maintenance.create',
            'operations.maintenance.edit',
            'operations.maintenance.delete',

            'operations.fieldservice.view',
            'operations.fieldservice.create',
            'operations.fieldservice.edit',
            'operations.fieldservice.delete',
            'operations.fieldservice.dispatch',

            'operations.quality.view',
            'operations.quality.create',
            'operations.quality.edit',
            'operations.quality.delete',

            'operations.hse.view',
            'operations.hse.create',
            'operations.hse.edit',
            'operations.hse.delete',

            // ── Analytics ──
            'analytics.dashboards.view',
            'analytics.dashboards.create',
            'analytics.dashboards.edit',
            'analytics.dashboards.delete',

            'analytics.reports.view',
            'analytics.reports.create',
            'analytics.reports.export',

            'analytics.kpis.view',
            'analytics.kpis.create',
            'analytics.kpis.edit',

            // ── IT ──
            'it.assets.view',
            'it.assets.create',
            'it.assets.edit',
            'it.assets.delete',

            'it.helpdesk.view',
            'it.helpdesk.create',
            'it.helpdesk.edit',
            'it.helpdesk.delete',
            'it.helpdesk.assign',

            'it.saas.view',
            'it.saas.edit',

            'it.access.view',
            'it.access.edit',

            'it.security.view',
            'it.security.edit',

            'it.monitoring.view',
            'it.monitoring.edit',

            // ── Legal ──
            'legal.contracts.view',
            'legal.contracts.create',
            'legal.contracts.edit',
            'legal.contracts.delete',
            'legal.contracts.sign',

            'legal.policies.view',
            'legal.policies.create',
            'legal.policies.edit',
            'legal.policies.delete',
            'legal.policies.publish',

            'legal.risks.view',
            'legal.risks.create',
            'legal.risks.edit',
            'legal.risks.delete',

            'legal.privacy.view',
            'legal.privacy.edit',

            // ── Ecommerce ──
            'ecommerce.products.view',
            'ecommerce.products.create',
            'ecommerce.products.edit',
            'ecommerce.products.delete',

            'ecommerce.orders.view',
            'ecommerce.orders.create',
            'ecommerce.orders.edit',
            'ecommerce.orders.delete',
            'ecommerce.orders.fulfill',
            'ecommerce.orders.refund',

            'ecommerce.storefront.view',
            'ecommerce.storefront.edit',
            'ecommerce.storefront.publish',

            'ecommerce.subscriptions.view',
            'ecommerce.subscriptions.edit',

            // ── Communications ──
            'communications.messaging.view',
            'communications.messaging.create',
            'communications.messaging.delete',

            'communications.announcements.view',
            'communications.announcements.create',
            'communications.announcements.edit',
            'communications.announcements.delete',
            'communications.announcements.publish',

            'communications.intranet.view',
            'communications.intranet.edit',

            'communications.bookings.view',
            'communications.bookings.create',
            'communications.bookings.edit',
            'communications.bookings.delete',

            // ── Learning ──
            'learning.courses.view',
            'learning.courses.create',
            'learning.courses.edit',
            'learning.courses.delete',
            'learning.courses.publish',

            'learning.skills.view',
            'learning.skills.create',
            'learning.skills.edit',

            'learning.mentoring.view',
            'learning.mentoring.create',
            'learning.mentoring.edit',

            'learning.training.view',
            'learning.training.create',
            'learning.training.edit',
            'learning.training.approve',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'tenant']);
        }

        // ── Roles ──

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'tenant']);
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'tenant')->get());

        $workspaceAdmin = Role::firstOrCreate(['name' => 'workspace-admin', 'guard_name' => 'tenant']);
        $workspaceAdmin->givePermissionTo(
            Permission::where('guard_name', 'tenant')
                ->where('name', 'not like', 'platform.%')
                ->get()
        );

        $hrManager = Role::firstOrCreate(['name' => 'hr-manager', 'guard_name' => 'tenant']);
        $hrManager->givePermissionTo(
            Permission::where('guard_name', 'tenant')
                ->where('name', 'like', 'hr.%')
                ->get()
        );

        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'tenant']);
        $employee->givePermissionTo([
            'hr.leave.view',
            'hr.leave.create',
            'projects.tasks.view',
            'projects.tasks.create',
            'projects.tasks.edit',
            'projects.time.view',
            'projects.time.create',
            'projects.time.edit',
            'projects.documents.view',
            'projects.wiki.view',
            'communications.messaging.view',
            'communications.messaging.create',
            'communications.announcements.view',
            'communications.intranet.view',
            'communications.bookings.view',
            'communications.bookings.create',
            'learning.courses.view',
            'learning.skills.view',
            'learning.mentoring.view',
        ]);
    }
}
