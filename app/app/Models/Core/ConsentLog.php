<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Append-only consent trail.
 *
 * @property string $id
 * @property string $company_id
 * @property string $user_id
 * @property string $data_category
 * @property Carbon $consented_at
 * @property Carbon|null $withdrawn_at
 */
class ConsentLog extends Model
{
    use BelongsToCompany, HasUlids;

    protected $fillable = [
        'company_id',
        'user_id',
        'data_category',
        'consented_at',
        'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'consented_at' => 'datetime',
            'withdrawn_at' => 'datetime',
        ];
    }
}
