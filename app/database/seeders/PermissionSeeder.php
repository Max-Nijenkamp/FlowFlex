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
        'core.rbac.assign-roles',
        'core.rbac.transfer-ownership',
        // core.invitations
        'core.invitations.view-any',
        'core.invitations.send',
        'core.invitations.revoke',
        // core.billing-engine
        'core.billing.view',
        'core.billing.manage',
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
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Artisan::call('permission:cache-reset');
    }
}
