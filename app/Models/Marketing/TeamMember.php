<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'role',
    'bio',
    'photo',
    'linkedin_url',
    'twitter_url',
    'display_order',
    'is_published',
])]
class TeamMember extends Model
{
    use HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
