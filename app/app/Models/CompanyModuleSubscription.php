<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\CompanyModuleSubscriptionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * One row per activation event (architecture/module-system.md): deactivation
 * stamps deactivated_at and keeps the row; reactivation creates a new row.
 *
 * @property string $id
 * @property string $company_id
 * @property string $module_key
 * @property Carbon $activated_at
 * @property ?Carbon $deactivated_at
 * @property ?string $activated_by
 */
class CompanyModuleSubscription extends Model
{
    use BelongsToCompany;

    /** @use HasFactory<CompanyModuleSubscriptionFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'module_key',
        'activated_at',
        'deactivated_at',
        'activated_by',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }
}
