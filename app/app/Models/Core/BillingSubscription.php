<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingSubscription extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'stripe_customer_id',
        'stripe_subscription_id',
        'status',
        'user_count',
        'monthly_amount',
        'currency',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at'         => 'datetime',
        'current_period_start'  => 'datetime',
        'current_period_end'    => 'datetime',
        'canceled_at'           => 'datetime',
        'ends_at'               => 'datetime',
        'monthly_amount'        => 'decimal:2',
    ];

    public function isActive(): bool
    {
        return in_array($this->status, ['trialing', 'active'], true);
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && $this->trial_ends_at?->isFuture();
    }
}
