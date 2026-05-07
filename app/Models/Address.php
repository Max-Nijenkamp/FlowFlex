<?php

namespace App\Models;

use App\Enums\Country;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'country',
    'city',
    'street',
    'postal_code',
    'house_number',
    'house_number_addition',
    'is_primary',
])]
class Address extends Model
{
    use HasUlids, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'country'    => Country::class,
            'is_primary' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['country', 'city', 'street', 'postal_code', 'house_number', 'house_number_addition', 'is_primary'])
            ->logOnlyDirty();
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
