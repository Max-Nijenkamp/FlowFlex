<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Database\Factories\Core\CompanyModuleSubscriptionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $company_id
 * @property string $module_key
 * @property Carbon $activated_at
 * @property Carbon|null $deactivated_at
 * @property string|null $activated_by
 */
class CompanyModuleSubscription extends Model
{
    /** @use HasFactory<CompanyModuleSubscriptionFactory> */
    use BelongsToCompany, HasFactory, HasUlids;

    protected $fillable = [
        'company_id',
        'module_key',
        'activated_at',
        'deactivated_at',
        'activated_by',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }
}
