<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'role',
    'company',
    'quote',
    'photo',
    'is_featured',
    'display_order',
    'is_published',
])]
class Testimonial extends Model
{
    use HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_featured'  => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
