<?php

declare(strict_types=1);

namespace App\Models\Foundation;

use App\Models\Company;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    use HasUlids;

    public $timestamps = false;

    protected $dates = ['created_at'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
