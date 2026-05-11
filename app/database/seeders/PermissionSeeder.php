<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        // Foundation (Phase 0)
        'core.users.view',
        'core.users.create',
        'core.users.edit',
        'core.users.delete',
        'core.users.impersonate',
        'core.company.settings.manage',
        'core.roles.manage',
        'core.modules.manage',
        'core.announcements.view',
        'core.announcements.manage',

        // Core Platform (Phase 1)
        'core.audit.view-own-actions',
        'core.audit.view-team',
        'core.audit.view-any',
        'core.audit.export',
        'core.notifications.manage-own-preferences',
        'core.notifications.manage-team-defaults',
        'core.notifications.view-delivery-log',
        'core.import.create',
        'core.import.rollback',
        'core.import.view-history',
        'core.sandbox.create',
        'core.sandbox.clone-from-production',
        'core.sandbox.reset',
        'core.sandbox.promote-config',
        'core.sandbox.manage-access',
        'core.api.manage-clients',
        'core.api.manage-webhooks',
        'core.api.view-logs',
        'core.billing.view',
        'core.billing.manage',

        // HR & People (Phase 2)
        'hr.employees.view-any',
        'hr.employees.view',
        'hr.employees.create',
        'hr.employees.edit',
        'hr.employees.delete',
        'hr.onboarding.view',
        'hr.onboarding.manage',
        'hr.leave.view-any',
        'hr.leave.request',
        'hr.leave.approve',
        'hr.leave.manage-policy',
        'hr.payroll.view',
        'hr.payroll.run',
        'hr.payroll.approve',
        'hr.payroll.export',
        'hr.analytics.view',
        'hr.analytics.export',

        // Projects & Work (Phase 2)
        'projects.projects.view-any',
        'projects.projects.view',
        'projects.projects.create',
        'projects.projects.edit',
        'projects.projects.delete',
        'projects.tasks.view-any',
        'projects.tasks.view',
        'projects.tasks.create',
        'projects.tasks.edit',
        'projects.tasks.delete',
        'projects.boards.view',
        'projects.boards.manage',
        'projects.gantt.view',
        'projects.gantt.manage',
        'projects.sprints.view',
        'projects.sprints.manage',
        'projects.time.view-own',
        'projects.time.view-any',
        'projects.time.log',
        'projects.time.approve',
        'projects.templates.view',
        'projects.templates.manage',
    ];

    public function run(): void
    {
        // Create all permissions idempotently
        foreach (self::PERMISSIONS as $perm) {
            Permission::firstOrCreate([
                'name'       => $perm,
                'guard_name' => 'web',
            ]);
        }

        // Sync all permissions to every owner role across all companies
        Role::where('name', 'owner')
            ->where('guard_name', 'web')
            ->each(fn (Role $role) => $role->syncPermissions(Permission::all()));
    }
}
