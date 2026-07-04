<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ModuleCatalogEntryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Platform-level module catalog (architecture/module-system.md) — NOT
 * company-scoped: every tenant sees the same catalog. Prices are euro cents.
 *
 * @property string $id
 * @property string $module_key
 * @property string $domain
 * @property string $name
 * @property int $per_user_monthly_price
 * @property bool $is_active
 */
class ModuleCatalogEntry extends Model
{
    /** @use HasFactory<ModuleCatalogEntryFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $table = 'module_catalog';

    protected $fillable = [
        'module_key',
        'domain',
        'name',
        'per_user_monthly_price',
        'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'per_user_monthly_price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function isFree(): bool
    {
        return $this->per_user_monthly_price === 0;
    }
}
