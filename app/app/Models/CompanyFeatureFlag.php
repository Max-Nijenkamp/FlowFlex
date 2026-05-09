<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyFeatureFlag extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'company_id',
        'flag',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isGlobal(): bool
    {
        return $this->company_id === null;
    }
}
