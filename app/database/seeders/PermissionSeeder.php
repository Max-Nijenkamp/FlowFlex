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
        // Company workspace foundation (/app) — see foundation.panels.
        'company.settings.view-any',
        'company.settings.update',
        'company.users.view-any',
        'company.users.create',
        'company.users.update',
        'company.users.delete',
        'company.roles.view-any',
        'company.roles.manage',
    ];

    public function run(): void
    {
        Artisan::call('permission:cache-reset');

        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }
}
