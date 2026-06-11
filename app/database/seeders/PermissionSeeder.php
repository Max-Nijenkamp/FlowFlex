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
    ];

    public function run(): void
    {
        Artisan::call('permission:cache-reset');

        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }
}
