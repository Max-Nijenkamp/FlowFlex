<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * Platform-level module catalog. NOT company-scoped. Backed by a static array
 * (calebporzio/sushi) — catalog entries live in code, not the database.
 *
 * The whole Core Platform is included free with every subscription (always
 * active, non-deactivatable). Priced domain modules are appended as domains ship.
 *
 * @property string $module_key
 * @property string $domain
 * @property string $name
 * @property int $per_user_monthly_price_cents
 * @property bool $is_active
 * @property bool $is_free_core
 */
class ModuleCatalog extends Model
{
    use Sushi;

    protected $primaryKey = 'module_key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'per_user_monthly_price_cents' => 'integer',
        'is_active' => 'boolean',
        'is_free_core' => 'boolean',
    ];

    /** @var list<string> Core Platform modules — free + always active. */
    public const array FREE_CORE = [
        'core.auth',
        'core.settings',
        'core.rbac',
        'core.invitations',
        'core.billing',
        'core.marketplace',
        'core.audit',
        'core.notifications',
        'core.files',
        'core.import',
        'core.webhooks',
        'core.api',
        'core.setup',
        'core.privacy',
        'core.i18n',
        'core.health',
    ];

    /** @return array<int, array<string, mixed>> */
    public function getRows(): array
    {
        return array_values(self::entries());
    }

    /**
     * All catalog entries keyed by module_key: the free core set + paid domain
     * modules registered in config('flowflex.modules') as domains ship.
     * Logic reads these helpers directly (never the Sushi table) so the
     * catalog stays config-extensible and test-overridable.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function entries(): array
    {
        $entries = [];

        foreach (self::FREE_CORE as $key) {
            $entries[$key] = [
                'module_key' => $key,
                'domain' => explode('.', $key)[0],
                'name' => str($key)->after('.')->headline()->value(),
                'per_user_monthly_price_cents' => 0,
                'is_active' => true,
                'is_free_core' => true,
            ];
        }

        /** @var array<string, array<string, mixed>> $configured */
        $configured = config('flowflex.modules', []);

        foreach ($configured as $key => $module) {
            $entries[$key] = [
                'module_key' => $key,
                'domain' => $module['domain'] ?? explode('.', $key)[0],
                'name' => $module['name'] ?? str($key)->after('.')->headline()->value(),
                'per_user_monthly_price_cents' => (int) ($module['per_user_monthly_price_cents'] ?? 0),
                'is_active' => (bool) ($module['is_active'] ?? true),
                'is_free_core' => false,
            ];
        }

        return $entries;
    }

    /** @return array<string, mixed>|null */
    public static function entry(string $moduleKey): ?array
    {
        return self::entries()[$moduleKey] ?? null;
    }

    public static function priceCents(string $moduleKey): int
    {
        return (int) (self::entry($moduleKey)['per_user_monthly_price_cents'] ?? 0);
    }

    /** @return list<string> */
    public static function freeCoreModules(): array
    {
        return self::FREE_CORE;
    }

    public static function isFreeCore(string $moduleKey): bool
    {
        return in_array($moduleKey, self::FREE_CORE, true);
    }
}
