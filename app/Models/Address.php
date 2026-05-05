<?php

namespace App\Models;

use App\Enums\Country;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
    use HasUlids;

    protected function casts(): array
    {
        return [
            'country'    => Country::class,
            'is_primary' => 'boolean',
        ];
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
